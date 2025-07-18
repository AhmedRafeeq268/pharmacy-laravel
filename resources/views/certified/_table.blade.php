<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <th scope="col">@lang('messages.bill.total_amount')</th>
        <th scope="col">@lang('messages.bill.currency_type')</th>
        <th scope="col">@lang('messages.bill.bill_number')</th>
        <th scope="col">@lang('messages.bill.bill_date')</th>
        <th scope="col">@lang('messages.bill.receiving_employee')</th>
        <th scope="col">@lang('messages.bill.adopt_bill')</th>
        <th scope="col">@lang('messages.bill.authorized_employee')</th>
        <th scope="col">@lang('messages.bill.certified_or_not')</th>
    </thead>
    <tbody>
        @foreach ($bills as $bill)
            <td>{{ $bill->total_amount }}</td>
            <td>{{ $bill->currancy_type }}</td>
            <td>{{ $bill->bill_number }}</td>
            <td>{{ $bill->bill_date }}</td>
            <td>{{ $bill->employee_receipt }}</td>
            <td>{{ $bill->adopt_bill }}</td>
            <td>{{ $bill->authorized_employee }}</td>
            <td>{{ $bill->certified_or_not }}</td>
        @endforeach

    </tbody>

</table>
<table class="table table-bordered table-striped table-hover text-center">
    <thead>
        <th>Bill details</th>
    </thead>
</table>
<table class="table table-bordered table-striped table-hover text-center">

    <thead >
        <tr>
            <th scope="col">#</th>
            <th scope="col">bill_id</th>
            <th scope="col">product_name</th>
            <th scope="col">product_category</th>
            <th scope="col">product_data</th>
            <th scope="col">quantity</th>
            <th scope="col">cost</th>
            <th scope="col">discount</th>
            <th scope="col">total</th>


        </tr>
    </thead>
    <tbody class="table-group-divider">
        @php
            $id=1;
        @endphp

        @foreach ($billDetailsItems as $billItem)
            <tr>
                <th scope="row">{{$id++ }}</th>
                <td>{{ $billItem->bill_id }}</td>
                <td>{{ $billItem->product_name }}</td>
                <td>{{ $billItem->product_category }}</td>
                <td>{{ $billItem->product_data }}</td>
                <td>{{ $billItem->quantity }}</td>
                <td>{{ $billItem->cost }}</td>
                <td>{{ $billItem->discount }}</td>
                <td>{{ $billItem->total }}</td>

            </tr>
                @endforeach
    </tbody>
</table>
