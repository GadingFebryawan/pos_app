<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Modules\Sale\Jobs\ImportSalesJob;

class SalesImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $notes;

    public function __construct($notes)
    {
        $this->notes = $notes;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $requestData = [
                "reference" => "SL",
                "customer_id" => null,
                "customer_name" => $row['customer_name'],
                "customer_address" => $row['customer_address'],
                "customer_country" => null,
                "customer_nik" => null,
                "date" => date("Y-m-d"),
                "shipping_amount" => "0",
                "total_amount" => $row['amount'],
                "payment_code" => $row['unique_id'],
                "tax_percentage" => "0",
                "discount_percentage" => "0",
                "status" => "Pending",
                "payment_method" => strtoupper($row['channel']),
                "paid_amount" => "0",
                "item_name" => $row['item_name'],
                "item_qty" => 1,
                "item_amount" => $row['amount'],
                "note" => $this->notes,
            ];

            $saleItems[] = [
                'product_name' => $row['item_name'],
                'product_code' => $row['item_code'] ?? null,
                'quantity' => $row['item_qty'] ?? 1,
                'price' => $row['amount'],
                'sub_total' => $row['amount'],
            ];
            $requestData['sale_items'] = $saleItems;

            if (!empty($row['amount'])) {
                ImportSalesJob::dispatch($requestData);
            }
        }
    }
}
