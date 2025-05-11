<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Purchase\Entities\Purchase;
use Modules\Sale\Entities\Sale;

class PGService
{
    protected $smService;

    public function __construct(SmartlinkService $smService)
    {
        $this->smService = $smService;
    }

    public function createPaymentToVendor(Sale $sale)
    {
        try {
            Log::info("createPaymentToVendor(" . $sale->reference . ") - starting");
            $vendorRequest =  $this->smService->generateRequestSmartlink($sale);
            $credentials =  $this->smService->getCredential($sale->company_id);
            $vendorResponse =  $this->smService->generatePaymentSmartlink($vendorRequest, $credentials);
            if ($vendorResponse['status'] != "success") {
                $sale->status = "Failed";
                $sale->save();
                return [false, $vendorResponse['message'] ?? "Something went wrong"];
            }

            if (!empty($vendorResponse['data']) && $vendorResponse['status'] == "success") {
                $dataVendor = $vendorResponse['data'];
                $sale->payment_code = $dataVendor['payment_details']['payment_code'];
                // $sale->exp_date = $dataVendor['payment_details']['expired_time'];
                $sale->exp_date = Carbon::parse($dataVendor['payment_details']['expired_time'])->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');
                $sale->vendor_trans_id = $dataVendor['transaction_id'];
                $sale->save();
            }
            Log::info("createPaymentToVendor(" . $sale->reference . ") - success", [$vendorResponse]);
            return [true, null];
        } catch (\Throwable $th) {
            Log::info("createPaymentToVendor(" . $sale->reference . ") - error : " . $th->getMessage() . ", at " . $th->getFile() . ", line:" . $th->getLine() . ")");
            return [false, $th->getMessage()];
        }
    }

    public function getListChannelVendor()
    {
        try {
            Log::info("getListChannelVendor - starting");

            $userCompanyIds = Auth::user()->companies()->pluck('companies.id')->toArray();
            $credentials =  $this->smService->getCredential($userCompanyIds[0]);

            $keyCache = hash('sha256', "getListChannelVendor" . $userCompanyIds[0]);
            if (Cache::has($keyCache)) {
                Log::info("getListChannelVendor - success from cache");
                return Cache::get($keyCache);
            }
            $vendorResponse =  $this->smService->getListChannelSmartlink($credentials);

            if (empty($vendorResponse)) {
                return [];
            }
            $channelNames = [];

            $data = $vendorResponse['data'] ?? [];

            foreach ($data as $key => $channels) {
                foreach ($channels as $channel) {
                    $channelNames[] = $channel['name'];
                }
            }

            Log::info("getListChannelVendor - success", ['channel_names' => $channelNames]);
            Cache::put($keyCache, $channelNames, now()->addMinutes(15));
            return $channelNames;
        } catch (\Throwable $th) {
            Log::info("getListChannelVendor - error : " . $th->getMessage() . ", at " . $th->getFile() . ", line:" . $th->getLine() . ")");
            return [];
        }
    }
}
