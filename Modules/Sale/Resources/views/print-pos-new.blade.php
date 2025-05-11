<!DOCTYPE html>
<html>

<head>
    <title>@lang('pdf_invoice_label') - {{ $sale->invoice_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.css" integrity="sha512-6g9IGCc67eh+xK03Z8ILcnKLbJnKBW+qpEdoUVD/4hBa2Ghiq5dQgeNOGWJfGoe9tdCRM4GpJMnsRXa2FDJp9Q==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    <style type="text/css">
        /* -- Base -- */
        body {
            font-family: "DejaVu Sans";
        }

        html {
            margin: 0px;
            padding: 0px;
            margin-top: 50px;

        }

        .text-center {
            text-align: center;
        }

        hr {
            margin: 0 30px 0 30px;
            color: rgba(0, 0, 0, 0.2);
            border: 0.5px solid #EAF1FB;
        }

        /* -- Header -- */

        .header-bottom-divider {
            color: rgba(0, 0, 0, 0.2);
            top: 90px;
            left: 0px;
            width: 100%;
            margin-left: 0%;

        }

        .header-container {
            position: absolute;
            width: 100%;
            height: 90px;
            left: 0px;
            top: -50px;
        }

        .header-logo {
            margin-top: 20px;
            padding-bottom: 20px;
            text-transform: capitalize;
            color: #817AE3;

        }

        .header {
            font-size: 20px;
            color: rgba(0, 0, 0, 0.7);
        }

        .content-wrapper {
            display: block;
            margin-top: 0px;
            padding-top: 16px;
            padding-bottom: 20px;
        }

        .company-address-container {
            padding-top: 15px;
            padding-left: 30px;
            float: left;
            width: 30%;
            margin-bottom: 2px;
        }

        .company-address-container h1 {
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            margin-bottom: 0px;
            margin-top: 10px;
        }

        .company-address {
            margin-top: 16px;
            text-align: left;
            font-size: 12px;
            line-height: 15px;
            color: #595959;
            width: 280px;
            word-wrap: break-word;
        }

        .company-address-new {
            text-align: right;
            font-size: 10px;
            line-height: 12px;
            color: #595959;
            width: 280px;
            word-wrap: break-word;
        }

        .invoice-details-container {
            float: right;
            padding: 10px 30px 0 0;
            margin-top: 18px;
        }

        .attribute-label {
            font-size: 12px;
            line-height: 18px;
            padding-right: 40px;
            text-align: left;
            color: #55547A;
        }

        .attribute-value {
            font-size: 12px;
            line-height: 18px;
            text-align: right;
        }

        .attribute-btn {
            position: absolute;
            font-size: 18px;
            /* line-height: 18px; */
            text-align: center;
            margin-left: 35px;
        }

        .button {
            background-color: #6e68dd;
            /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }

        .button-danger {
            background-color: #ff0000;
            /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }

        /* -- Shipping -- */

        .shipping-address-container {
            float: right;
            padding-left: 40px;
            width: 160px;
        }

        .shipping-address {
            font-size: 12px;
            line-height: 15px;
            color: #595959;
            padding: 45px 0px 0px 40px;
            margin: 0px;
            width: 160px;
            word-wrap: break-word;
        }

        /* -- Billing -- */

        .billing-address-container {
            padding-top: 50px;
            float: left;
            padding-left: 30px;
        }

        .billing-address-label {
            font-size: 12px;
            line-height: 18px;
            padding: 0px;
            margin-top: 27px;
            margin-bottom: 0px;
        }

        .billing-address-name {
            max-width: 160px;
            font-size: 15px;
            line-height: 22px;
            padding: 0px;
            margin: 0px;
        }

        .billing-address {
            font-size: 12px;
            line-height: 15px;
            color: #595959;
            padding: 45px 0px 0px 30px;
            margin: 0px;
            width: 160px;
            word-wrap: break-word;
        }

        /* -- Items Table -- */

        .items-table {
            margin-top: 35px;
            padding: 0px 30px 10px 30px;
            page-break-before: avoid;
            page-break-after: auto;
        }

        .items-table hr {
            height: 0.1px;
        }

        .item-table-heading {
            font-size: 13.5;
            text-align: center;
            color: rgba(0, 0, 0, 0.85);
            padding: 5px;
            color: #55547A;
        }

        tr.item-table-heading-row th {
            border-bottom: 0.620315px solid #E8E8E8;
            font-size: 12px;
            line-height: 18px;
        }

        tr.item-row td {
            font-size: 12px;
            line-height: 18px;
        }

        .item-cell {
            font-size: 13;
            text-align: center;
            padding: 5px;
            padding-top: 10px;
            color: #040405;
        }

        .item-description {
            color: #595959;
            font-size: 9px;
            line-height: 12px;
        }

        /* -- Total Display Table -- */

        .total-display-container {
            padding: 0 25px;
        }

        .total-display-table {
            border-top: none;
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
            margin-top: 20px;
            float: right;
            width: auto;
        }

        .total-table-attribute-label {
            font-size: 13px;
            color: #55547A;
            text-align: left;
            padding-left: 10px;
        }

        .total-table-attribute-value {
            font-weight: bold;
            text-align: right;
            font-size: 13px;
            color: #040405;
            padding-right: 10px;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .total-border-left {
            border: 1px solid #E8E8E8 !important;
            border-right: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        .total-border-right {
            border: 1px solid #E8E8E8 !important;
            border-left: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        /* -- Notes -- */

        .notes {
            font-size: 12px;
            color: #595959;
            margin-top: 15px;
            margin-left: 30px;
            width: 442px;
            text-align: left;
            page-break-inside: avoid;
        }

        .notes-label {
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            color: #040405;
            width: 108px;
            white-space: nowrap;
            height: 19.87px;
            padding-bottom: 10px;
        }

        /* -- Helpers -- */

        .text-primary {
            color: #5851DB;
        }


        table .text-left {
            text-align: left;
        }

        table .text-right {
            text-align: right;
        }

        .border-0 {
            border: none;
        }

        .py-2 {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .py-8 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .py-3 {
            padding: 3px 0;
        }

        .pr-20 {
            padding-right: 20px;
        }

        .pr-10 {
            padding-right: 10px;
        }

        .pl-20 {
            padding-left: 20px;
        }

        .pl-10 {
            padding-left: 10px;
        }

        .pl-0 {
            padding-left: 0;
        }

        /* .header-logo {
            height: 100px;
        }

        .company-address-container {
            text-align: center;
        }

        .table-layout td {
            vertical-align: middle;
        }

        .table-layout td:first-child {
            text-align: left;
            vertical-align: top;
        }

        .table-layout td:last-child {
            text-align: right;
            vertical-align: top;
        } */
    </style>
</head>

<body>
    <div class="header-container">
        <table width="100%" style="border-collapse: collapse; margin-top: 50px;">
            <tr>
                <td style="text-align: left; vertical-align: top; width: 30%;">
                    <img class="header-logo" style="height: 50px;"
                        src="https://smartlink.pakar-digital.com/storage/214/photos" alt="Company Logo">
                </td>

                <td style="width: 20%;"></td>

                <td style="text-align: right; vertical-align: top; width: 50%;">
                    <div class="invoice-details-container company-address-new">
                        <strong>{{ $company->name }}</strong>
                        <br>
                        @if (!empty($company->address))
                            <span>{!! nl2br(e($company->address)) !!}</span>
                        @else
                            <span>-</span>
                        @endif
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <div class="text-center" style="font-size: 14px">
                        <strong>SPTSA</strong><br>
                        <strong>SURAT PEMBERITAHUAN TAGIHAN SHARING AGROFORESTRY</strong>
                    </div>
                </td>
            </tr>
        </table>
        <hr class="header-bottom-divider" style="border: 0.620315px solid #E8E8E8;" />
    </div>

    <div class="content-wrapper">
        <div style="padding-top: 50px">
            <div class="company-address-container company-address">
                <strong>Kepada,</strong> </br>
                {{ $sale->customer_name }} </br>
                {{ $customer->nik ?? '-' }} </br>
                {!! $customer->address ?? '-' !!} </br>
                {{ $customer->country ?? '-' }} </br>
            </div>

            <div class="invoice-details-container">
                <table>
                    <tr>
                        <td class="attribute-label">Reference</td>
                        <td class="attribute-value"> &nbsp;{{ $sale->reference }}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">Date</td>
                        <td class="attribute-value"> &nbsp;{{ $sale->date }}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">Exp Date</td>
                        <td class="attribute-value"> &nbsp;{{ !empty($sale->exp_date) ? $sale->exp_date : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">Paid Date</td>
                        <td class="attribute-value"> &nbsp;{{ !empty($sale->paid_date) ? $sale->paid_date : '-' }}</td>
                    </tr>
                </table>
            </div>

            <div style="clear: both;"></div>
        </div>

        {{-- @php
            $billing_address = 'ini billing address, alamatnnya disini';
            $shipping_address = 'ini shipping address, alamatnnya disini';
        @endphp
        <div class="billing-address-container billing-address">
            @if (!empty($billing_address))
                <b>Billing Address</b> <br>

                {!! $billing_address !!}
            @endif
        </div>

        @if (!empty($shipping_address))
            <div class="shipping-address-container shipping-address"
                @if ($billing_address !== '</br>') style="float:left;" @else style="display:block; float:left: padding-left: 0px;" @endif>
                <b>Shipping Address</b> <br>

                {!! $shipping_address !!}
            </div>
        @endif --}}

        <div style="position: relative; clear: both;">
            @include('sale::partials.table')
        </div>

        @php
            $notes = $sale->note ?? 'Tidak ada catatan';
        @endphp
        <div class="notes">
            @if (!empty($notes))
                <div class="notes-label">
                    Catatan
                </div>

                {!! nl2br(e($notes)) !!}
            @endif
        </div>

        <div class="text-center" style="font-size:16px; color: #000; margin-top:50px">
            Kode Pembayaran Melalui {!! $sale->payment_method !!}
        </div>
        <div class="text-center" style="font-size:24px; color: #000; margin-top:10px">
            <strong>{!! $sale->payment_code !!}</strong>
        </div>
    </div>
</body>

</html>
