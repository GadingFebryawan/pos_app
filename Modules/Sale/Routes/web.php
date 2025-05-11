<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;

Route::group(['middleware' => 'auth'], function () {

    //POS
    Route::get('/app/pos', 'PosController@index')->name('app.pos.index');
    Route::post('/app/pos', 'PosController@store')->name('app.pos.store');

    //Generate PDF
    Route::get('/sales/pdf/{id}', function ($id) {
        $sale = \Modules\Sale\Entities\Sale::findOrFail($id);
        $customer = \Modules\People\Entities\Customer::findOrFail($sale->customer_id);

        $pdf = \PDF::loadView('sale::print', [
            'sale' => $sale,
            'customer' => $customer,
        ])->setPaper('a4');

        return $pdf->stream('sale-'. $sale->reference .'.pdf');
    })->name('sales.pdf');

    Route::get('/sales/pos/pdf/{id}', function ($id) {
        $userCompanyIds = Auth::user()->companies()->pluck('companies.id')->toArray();
        $sale = \Modules\Sale\Entities\Sale::with(['saleCustomers', 'saleItems'])->findOrFail($id);
        
        abort_if(!in_array($sale->company_id, $userCompanyIds), 401);
        $company = \app\Models\Companies::where('id', $sale->company_id)->first();

        // $pdf = \PDF::loadView('sale::print-pos', [
        $pdf = \PDF::loadView('sale::print-pos-new', [
            'sale' => $sale,
            'customer' => $sale->saleCustomers,
            'items' => $sale->saleItems,
            'company' => $company,

        ])->setPaper('a5')
            ->setOption('margin-top', 8)
            ->setOption('margin-bottom', 8)
            ->setOption('margin-left', 5)
            ->setOption('margin-right', 5);
        return $pdf->stream('sale-'. $sale->reference .'.pdf');
    })->name('sales.pos.pdf');

    //Sales
    Route::resource('sales', 'SaleController');
    Route::post('/sales-upload', 'SaleController@storeByXlsx')->name('sales-upload.storeByXlsx');
    Route::get('/sales-download-template', 'SaleController@downloadTemplateXlsx')->name('sales-upload.downloadTemplate');

    //Payments
    Route::get('/sale-payments/{sale_id}', 'SalePaymentsController@index')->name('sale-payments.index');
    Route::get('/sale-payments/{sale_id}/create', 'SalePaymentsController@create')->name('sale-payments.create');
    Route::post('/sale-payments/store', 'SalePaymentsController@store')->name('sale-payments.store');
    Route::get('/sale-payments/{sale_id}/edit/{salePayment}', 'SalePaymentsController@edit')->name('sale-payments.edit');
    Route::patch('/sale-payments/update/{salePayment}', 'SalePaymentsController@update')->name('sale-payments.update');
    Route::delete('/sale-payments/destroy/{salePayment}', 'SalePaymentsController@destroy')->name('sale-payments.destroy');
});
