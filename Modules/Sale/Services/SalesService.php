<?php

namespace Modules\Sale\Services;

use App\Services\PGService;
use App\Services\SmartlinkService;
use Illuminate\Support\Facades\DB;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SaleDetails;

class SalesService
{
    /**
     * Insert sales import data into the database.
     *
     * @param array $dataRequest
     * @throws \Exception
     */
    public function insertDataSalesImport(array $dataRequest)
    {
        DB::transaction(function () use ($dataRequest) {
            // Calculate due amount and payment status
            $dueAmount = $this->calculateDueAmount($dataRequest['total_amount'], $dataRequest['paid_amount']);
            $paymentStatus = $this->determinePaymentStatus($dueAmount, $dataRequest['total_amount']);

            // Create a new sale
            $sale = $this->createSale($dataRequest, $dueAmount, $paymentStatus);
            // Create transaction for vendor payment
            $this->processVendorPayment($sale);
        });
    }

    /**
     * Calculate the due amount.
     *
     * @param float $totalAmount
     * @param float $paidAmount
     * @return float
     */
    private function calculateDueAmount(float $totalAmount, float $paidAmount): int
    {
        return (int) $totalAmount - (int) $paidAmount;
    }

    /**
     * Determine the payment status based on due amount and total amount.
     *
     * @param float $dueAmount
     * @param float $totalAmount
     * @return string
     */
    private function determinePaymentStatus(float $dueAmount, float $totalAmount): string
    {
        if ($dueAmount == $totalAmount) {
            return 'Unpaid';
        }

        return $dueAmount > 0 ? 'Partial' : 'Paid';
    }

    /**
     * Create a new sale record in the database.
     *
     * @param array $dataRequest
     * @param float $dueAmount
     * @param string $paymentStatus
     * @return Sale
     */
    private function createSale(array $dataRequest, int $dueAmount, string $paymentStatus): Sale
    {
        $sale = Sale::create([
            'date' => $dataRequest['date'],
            'customer_id' => 1,
            'customer_name' => $dataRequest['customer_name'],
            'tax_percentage' => $dataRequest['tax_percentage'],
            'discount_percentage' => $dataRequest['discount_percentage'],
            'shipping_amount' => $dataRequest['shipping_amount'] * 100,
            'paid_amount' => $dataRequest['paid_amount'] * 100,
            'total_amount' => $dataRequest['total_amount'] * 100,
            'due_amount' => $dueAmount * 100,
            'status' => $dataRequest['status'],
            'payment_status' => $paymentStatus,
            'payment_method' => $dataRequest['payment_method'],
            'payment_code' => $dataRequest['payment_code'],
            'note' => $dataRequest['note'] ?? null,
            'tax_amount' => 0,
            'discount_amount' => 0,
        ]);

        $sale->saleCustomers()->create([
            'sale_id' => $sale->id,
            'name' => $dataRequest['customer_name'],
            'email' => $dataRequest['customer_email'] ?? null,
            'phone' => $dataRequest['customer_phone'] ?? null,
            'address' => $dataRequest['customer_address'] ?? null,
            'country' => $dataRequest['customer_country'] ?? null,
            'nik' => $dataRequest['customer_nik'] ?? null
        ]);

        $saleItems = [];
        foreach ($dataRequest['sale_items'] as $key => $item) {
            $saleItems[] = [
                'sale_id' => $sale->id,
                'product_name' => $item['product_name'],
                'product_code' => $item['product_code'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'price' => $item['price'],
                'sub_total' => $item['sub_total'],
            ];
        }
        $sale->saleItems()->createMany($saleItems);

        SaleDetails::create([
            'sale_id' => $sale->id,
            'product_id' => 1,
            'product_name' => $dataRequest['item_name'],
            'product_code' => $dataRequest['item_code'] ?? "PKB001",
            'quantity' => $dataRequest['item_qty'],
            'price' => $dataRequest['item_amount'] * 100,
            'unit_price' => $dataRequest['item_amount'] * 100,
            'sub_total' => ((int) $dataRequest['item_qty'] * (int) $dataRequest['item_amount']) * 100,
            'product_discount_amount' => 0,
            'product_discount_type' => 0,
            'product_tax_amount' => 0,
        ]);

        return $sale;
    }

    /**
     * Process vendor payment for the given sale.
     *
     * @param Sale $sale
     * @throws \Exception
     */
    private function processVendorPayment(Sale $sale): void
    {
        $pgService = new PGService(new SmartlinkService());
        [$isSuccess, $message] = $pgService->createPaymentToVendor($sale);

        if (!$isSuccess) {
            throw new \Exception($message ?? 'Failed to create payment to vendor');
        }
    }
}
