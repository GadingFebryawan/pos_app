<?php

namespace Modules\Sale\Http\Controllers;

use App\Services\PGService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;
use Modules\Sale\Entities\SalePayment;
use Modules\Sale\Http\Requests\StorePosSaleRequest;

class PosController extends Controller
{

    protected $pgService;

    public function __construct(PGService $pgService)
    {
        $this->pgService = $pgService;
    }

    public function index()
    {
        Cart::instance('sale')->destroy();

        $customers = Customer::all();
        $product_categories = Category::all();

        return view('sale::pos.index', compact('product_categories', 'customers'));
    }


    public function store(StorePosSaleRequest $request)
    {
        try {
            $saleID = null;
            DB::transaction(function () use ($request, &$saleID) {
                $due_amount = $request->total_amount - $request->paid_amount;
    
                if ($due_amount == $request->total_amount) {
                    $payment_status = 'Unpaid';
                } elseif ($due_amount > 0) {
                    $payment_status = 'Partial';
                } else {
                    $payment_status = 'Paid';
                }
                $customer = Customer::findOrFail($request->customer_id);
                $sale = Sale::create([
                    'date' => now()->format('Y-m-d'),
                    'reference' => 'PSL',
                    'customer_id' => $request->customer_id,
                    'customer_name' => $customer->customer_name,
                    'tax_percentage' => $request->tax_percentage,
                    'discount_percentage' => $request->discount_percentage,
                    'shipping_amount' => $request->shipping_amount * 100,
                    'paid_amount' => $request->paid_amount * 100,
                    'total_amount' => $request->total_amount * 100,
                    'due_amount' => $due_amount * 100,
                    'status' => 'Completed',
                    'payment_status' => $payment_status,
                    'payment_method' => $request->payment_method,
                    'note' => $request->note,
                    'tax_amount' => Cart::instance('sale')->tax() * 100,
                    'discount_amount' => Cart::instance('sale')->discount() * 100,
                ]);
                $sale->saleCustomers()->create([
                    'sale_id' => $sale->id,
                    'name' => $customer->customer_name,
                    'email' => $customer->customer_email ?? null,
                    'phone' => $customer->customer_phone ?? null,
                    'address' => $customer->address ?? null,
                    'country' => $customer->country ?? null,
                    'nik' => $customer->city ?? null
                ]);
    
                $saleItems = [];
                foreach (Cart::instance('sale')->content() as $cart_item) {
                    SaleDetails::create([
                        'sale_id' => $sale->id,
                        'product_id' => $cart_item->id,
                        'product_name' => $cart_item->name,
                        'product_code' => $cart_item->options->code,
                        'quantity' => $cart_item->qty,
                        'price' => $cart_item->price * 100,
                        'unit_price' => $cart_item->options->unit_price * 100,
                        'sub_total' => $cart_item->options->sub_total * 100,
                        'product_discount_amount' => $cart_item->options->product_discount * 100,
                        'product_discount_type' => $cart_item->options->product_discount_type,
                        'product_tax_amount' => $cart_item->options->product_tax * 100,
                    ]);
    
                    $saleItems[] = [
                        'product_name' => $cart_item->name,
                        'product_code' => $cart_item->options->code,
                        'quantity' => $cart_item->qty,
                        'price' => $cart_item->price,
                        'sub_total' => $cart_item->options->sub_total,
                    ];
    
                    $product = Product::findOrFail($cart_item->id);
                    $product->update([
                        'product_quantity' => $product->product_quantity - $cart_item->qty
                    ]);
                }
                $sale->saleItems()->createMany($saleItems);
                $saleID = $sale->id;

                Cart::instance('sale')->destroy();
    
                if ($sale->paid_amount > 0) {
                    SalePayment::create([
                        'date' => now()->format('Y-m-d'),
                        'reference' => 'INV/' . $sale->reference,
                        'amount' => $sale->paid_amount,
                        'sale_id' => $sale->id,
                        'payment_method' => $request->payment_method
                    ]);
                    
                }
                
                [$isSuccess, $message] = $this->pgService->createPaymentToVendor($sale);
                if (!$isSuccess) {
                    throw new \Exception($message ?? 'Failed to create payment to vendor');
                }
            });
    
            toast('POS Sale Created!', 'success');
    
            if (empty($saleID)) {
                return redirect()->route('sales.index');
            }
    
            return redirect()->route('sales.pos.pdf', ['id' => $saleID]);
        } catch (\Throwable $e) {
            Log::error('Sale creation failed', ['error' => $e->getMessage()]);
            toast($e->getMessage() ?? 'Sale creation failed. Please try again.', 'error');
            return redirect()->back();
        }
    }
}
