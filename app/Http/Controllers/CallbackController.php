<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;

class CallbackController extends Controller
{
    public function callbackVendor(Request $request)
    {
        try {
            Log::info("callbackVendor", [$request->all()]);

            if (empty($request->status) && empty($request->data)) {
                return response()->json(['response' => 'callback status and data is empty'], 422);
            }
            $sale = Sale::where('reference', $request->data['order_id'])->first();
            if (empty($sale)) {
                Log::info("callbackVendor " . $sale->reference . " Sale already paid");
                return response()->json(['response' => 'SALE NOT FOUND'], 404);
            }
            if ($sale->status == "Completed") {
                Log::info("callbackVendor " . $sale->reference . " Sale already paid");
                return response()->json(['response' => 'Sale Already Paid'], 422);
            }
            $statusVendor = $request->data['status'];
            $statusSale = "Completed";
            $statusPaymentSale = "Paid";
            if ($statusVendor != "SUCCESS") {
                $statusSale = "Failed";
                $statusPaymentSale = "Unpaid";
            }

            $sale->status = $statusSale;
            $sale->payment_status = $statusPaymentSale;
            $sale->paid_amount = $request->data['amount'] * 100;
            $sale->save();
            Log::info("callbackVendor " . $sale->reference . " success update data");

            if ($statusVendor == "SUCCESS") {
                $sale->paid_date = $request->data['transaction_time'];
                $sale->save();
                SalePayment::create([
                    'date' => date('Y-m-d H:i:s'),
                    'reference' => 'INV/' . $sale->reference,
                    'amount' => $sale->paid_amount * 100,
                    'sale_id' => $sale->id,
                    'payment_method' => $sale->payment_method
                ]);
                Log::info("callbackVendor " . $sale->reference . " success create payment receipt");
            }

            Log::info("callbackVendor " . $sale->reference . " success proccess callback");
            return response()->json(['response' => 'OK']);
        } catch (\Throwable $th) {
            Log::info("callbackVendor error : " . $th->getMessage());
            return response()->json(['response' => 'ERROR RETRIEVE CALLBACK'], 500);
        }
    }
}
