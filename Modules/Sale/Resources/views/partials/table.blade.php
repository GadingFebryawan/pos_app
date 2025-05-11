<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr class="item-table-heading-row">
        <th width="2%" class="pr-20 text-right item-table-heading">#</th>
        <th width="40%" class="pl-0 text-left item-table-heading">Nama Barang</th>
        {{-- @foreach ($customFields as $field)
            <th class="text-right item-table-heading">{{ $field->label }}</th>
        @endforeach --}}
        <th class="pr-20 text-right item-table-heading">Qty</th>
        <th class="pr-20 text-right item-table-heading">Harga</th>
        @if ($sale->discount_per_item === 'YES')
            <th class="pl-10 text-right item-table-heading">Item Discount</th>
        @endif
        @if ($sale->tax_per_item === 'YES')
            <th class="pl-10 text-right item-table-heading">Item Tax</th>
        @endif
        <th class="text-right item-table-heading">Total Harga</th>
    </tr>
    @php
        $index = 1;
    @endphp
    @foreach ($items ?? [] as $item)
        <tr class="item-row">
            <td class="pr-20 text-right item-cell" style="vertical-align: top;">
                {{ $index }}
            </td>
            <td class="pl-0 text-left item-cell" style="vertical-align: top;">
                <span>{{ $item->product_name ?? "item name" }}</span><br>
                <span class="item-description">{!! nl2br(htmlspecialchars($item->product_code ?? "")) !!}</span>
            </td>
            @foreach ($customFields ?? [] as $field)
                <td class="text-right item-cell" style="vertical-align: top;">
                    {{ $item->getCustomFieldValueBySlug($field->slug) }}
                </td>
            @endforeach
            <td class="pr-20 text-right item-cell" style="vertical-align: top;">
                {{ $item->quantity ?? 1 }} @if ($item->unit_name ?? "")
                    {{ $item->unit_name ?? "" }}
                @endif
            </td>
            <td class="pr-20 text-right item-cell" style="vertical-align: top;">
                {{-- {!! format_money_pdf($item->price, $sale->customer->currency) !!} --}}
                {{ format_currency($item->price) }}
            </td>

            @if ($sale->discount_per_item === 'YES')
                <td class="pl-10 text-right item-cell" style="vertical-align: top;">
                    @if ($item->discount_type === 'fixed')
                        {{-- {!! format_money_pdf($item->discount_val, $sale->customer->currency) !!} --}}
                        0
                    @endif
                    @if ($item->discount_type === 'percentage')
                        {{ $item->discount }}%
                    @endif
                </td>
            @endif

            @if ($sale->tax_per_item === 'YES')
                <td class="pl-10 text-right item-cell" style="vertical-align: top;">
                    {{-- {!! format_money_pdf($item->tax, $sale->customer->currency) !!} --}}
                    0
                </td>
            @endif

            <td class="text-right item-cell" style="vertical-align: top;">
                {{-- {!! format_money_pdf($item->total, $sale->customer->currency) !!} --}}
                {{ format_currency($item->sub_total) }}
            </td>
        </tr>
        @php
            $index += 1;
        @endphp
    @endforeach
</table>

<hr class="item-cell-table-hr">

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0"
        class="total-display-table @if (count($sale->items ?? []) > 12) page-break @endif">
        {{-- <tr>
            <td class="border-0 total-table-attribute-label">Sub Total</td>
            <td class="py-2 border-0 item-cell total-table-attribute-value">
                {!! format_money_pdf($sale->sub_total ?? 0, $sale->customer->currency?? 0) !!}
                {{ format_currency($sale->due_amount) }}
            </td>
        </tr> --}}

        @if ($sale->discount > 0)
            @if ($sale->discount_per_item === 'NO')
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        @if ($sale->discount_type === 'fixed')
                            Discount
                        @endif
                        @if ($sale->discount_type === 'percentage')
                            Discount ({{ $sale->discount }}%)
                        @endif
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value">
                        @if ($sale->discount_type === 'fixed')
                            {{-- {!! format_money_pdf($sale->discount_val ?? 0, $sale->customer->currency ?? 0) !!} --}}
                            0
                        @endif
                        @if ($sale->discount_type === 'percentage')
                            {{-- {!! format_money_pdf($sale->discount_val ?? 0, $sale->customer->currency ?? 0) !!} --}}
                            0
                        @endif
                    </td>
                </tr>
            @endif
        @endif

        @if ($sale->tax_per_item === 'YES')
            @foreach ($taxes ?? [] as $tax)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{ $tax->name . ' (' . $tax->percent . '%)' }}
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value">
                        {{-- {!! format_money_pdf($tax->amount ?? 0, $sale->customer->currency ?? 0) !!} --}}
                        0
                    </td>
                </tr>
            @endforeach
        @else
            @foreach ($sale->taxes ?? [] as $tax)
                <tr>
                    <td class="border-0 total-table-attribute-label">
                        {{ $tax->name . ' (' . $tax->percent . '%)' }}
                    </td>
                    <td class="py-2 border-0 item-cell total-table-attribute-value">
                        {{-- {!! format_money_pdf($tax->amount ?? 0, $sale->customer->currency ?? 0) !!} --}}
                        0
                    </td>
                </tr>
            @endforeach
        @endif

        <tr>
            <td class="py-3"></td>
            <td class="py-3"></td>
        </tr>
        <tr>
            <td class="border-0 total-table-attribute-label" style="font-size:14px;">
                <strong>Total : </strong>
            </td>
            <td class="py-8 border-0 item-cell total-table-attribute-value" style="font-size:13px;">
                {{ format_currency($sale->total_amount) }}
            </td>
        </tr>
    </table>
</div>
