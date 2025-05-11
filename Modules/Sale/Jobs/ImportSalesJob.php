<?php

namespace Modules\Sale\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Sale\Services\SalesService;

class ImportSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dataRequest;
    public $tries = 4;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $dataRequest)
    {
        $this->onQueue('ImportSales');

        $this->dataRequest = $dataRequest;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() < $this->tries) {
            $saleService = new SalesService();
            if ($saleService->insertDataSalesImport($this->dataRequest)) {
                return $this->release(env('IMPORT_SALES_DELAY_SECONDS', 5));
            }
        }

        $this->delete();
    }
}
