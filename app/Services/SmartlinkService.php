<?php

namespace App\Services;

use App\Models\Companies;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Purchase\Entities\Purchase;
use Modules\Sale\Entities\Sale;

class SmartlinkService
{
    protected $headers;

    public function __construct()
    {
        $username = env('SMARTLINKPG_USERNAME', 'demo@pakar-digital.com');
        $password = env('SMARTLINKPG_PASSWORD', '12341234');
        $this->headers = [
            'Authorization' => 'Basic ' . base64_encode("$username:$password"),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function generateRequestSmartlink(Sale $sale)
    {
        $customer = $sale->saleCustomers()->first();
        $items = [];

        foreach ($sale->saleItems()->get() as $item) {
            $items[] = [
                "name" => $item->product_name,
                "amount" => $item->price,
                "qty" => $item->quantity,
            ];
        }

        return [
            "order_id" => $sale->reference,
            "amount" => $sale->due_amount,
            "customer" => [
                "name" => $customer->name,
                "email" => $customer->email ?? "custpost@gmail.com",
                "phone" => $customer->phone ?? "089723456712"
            ],
            "item" => $items,
            "channel" => [
                $sale->payment_method
            ],
            "type" => "json",
            "payment_mode" => $sale->payment_mode ?? "CLOSE",
            "payment_code" => $sale->payment_code ?? "",
            "expired_time" => Carbon::parse($sale->exp_date)->timezone('Asia/Jakarta')->format('Y-m-d\TH:i:sP'),
            "callback_url" => env("APP_URL", "invopos-sbx.pakar-digital.com") . "/api/invopos-callback",
            "success_redirect_url" => "https://cobadapetincallbacktest.free.beeceptor.com",
            "failed_redirect_url" => "https://cobadapetincallbacktest.free.beeceptor.com"
        ];
    }

    public function getCredential($companyID)
    {
        $company = Companies::where('id', $companyID)->first();
        if (!empty($company) && !empty($company->settings)) {
            $credential = json_decode($company->settings);
            return base64_encode("$credential->username:$credential->password");
        }
        return null;
    }

    public function generateInquiryRequestSmartlink()
    {
        return null;
    }

    public function generatePaymentSmartlink(array $dataRequest, $credentials)
    {
        $url = env('SMARTLINKPG_URL', 'https://payment-service-sbx.pakar-digital.com/api') . '/payment/create-order';
        if (!empty($credentials)) {
            $this->headers['Authorization'] = 'Basic ' . $credentials;
        }
        $response = Http::timeout(env('TIMEOUT_CURL_SECOND', 15))->withHeaders($this->headers)
            ->post($url, $dataRequest);

        $log = [
            'url' => $url,
            'header' => $this->headers,
            'body' => $dataRequest,
            'response' => json_decode($response->getBody(), true),
        ];
        if (!$response->successful()) {
            Log::info("generatePaymentSmartlink error : ", $log);
            return json_decode($response->getBody(), true);
        }

        Log::info("generatePaymentSmartlink success : ", $log);
        return json_decode($response->getBody(), true);
    }

    public function getListChannelSmartlink($credentials)
    {
        $url = env('SMARTLINKPG_URL', 'https://payment-service-sbx.pakar-digital.com/api') . '/payment/list-channel';
        if (!empty($credentials)) {
            $this->headers['Authorization'] = 'Basic ' . $credentials;
        }
        $response = Http::timeout(env('TIMEOUT_CURL_SECOND', 15))->withHeaders($this->headers)
            ->get($url);

        if (!$response->successful()) {
            Log::info("getListChannelSmartlink error : ", [$response->body()]);
            return null;
        }
        return json_decode($response->getBody(), true);
    }
}
