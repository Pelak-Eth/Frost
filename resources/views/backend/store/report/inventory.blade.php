@extends('master')
@section('title', 'Inventory Report')
@section('content')
    <div class="panel panel-info">
        <div class="panel-heading">
            <h2>
                <i class="fa fa-area-chart" aria-hidden="true"></i>
                Inventory Report
                <a href="/admin/store/report/sales" class="btn btn-success btn-raised pull-right">Sales Report</a>
                <a href="/admin/store/report/inventory/export?store={{ $store }}" class="btn btn-info btn-raised pull-right" style="margin-right:10px;">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export to Excel
                </a>
            </h2>

        </div>
        <div class="panel-body">
            <div class="col-md-2">
                <div class="form-group">
                    <select name="store" id="store-select" style="width:80%">
                        <option value="0">All Stores</option>
                        @foreach(config('store.stores') as $key => $storeName)
                            <option value="{{ $key }}" {{ ($store == $key) ? 'selected' : '' }}>{{ $storeName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-1">
                <button id="filter" class="btn btn-raised btn-success">
                    Generate
                </button>
            </div>
        </div>
    </div>
    <!-- Start Report Details -->
    <div class="row">
        <div class="col-md-3">
            <div class="well">
                <table class="table">
                    <tbody>
                    <tr>
                        <td><h4>Total Cost</h4></td>
                        <td><h4>${{ number_format($data['totals']['cost'],2) }}</h4></td>
                    </tr>
                    <tr>
                        <td><h4>Retail Value</h4></td>
                        <td><h4>${{ number_format($data['totals']['value'],2) }}</h4></td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-9">
            <div class="well">
                <table class="table" id="table">
                    <thead>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Cost</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Total Cost</th>
                        <th>Total Value</th>
                    </thead>
                    <tbody>
                        @foreach($data['products'] as $product_id => $pdata)
                            <tr>
                                <td><a href="/admin/store/products/{{ $product_id }}/show">{{ $pdata['name'] }}</a></td>
                                <td>{{ config('store.product_categories')[$pdata['category']] }}</td>
                                <td>${{ $pdata['cost'] }}</td>
                                <td>${{ $pdata['price'] }}</td>
                                <td>{{ $pdata['stock'] }}</td>
                                <td>${{ number_format($pdata['cost'] * $pdata['stock'],2) }}</td>
                                <td>${{ number_format($pdata['price'] * $pdata['stock'],2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#table').DataTable( {
            "order" : [[ 0, "asc" ]]
        });
    });
    $('#filter').on('click', function() {
        var store = "store=" + $('#store-select :selected').val();
        var url = "/admin/store/report/inventory?" + store;
        window.location.href = url;
    });
</script>
@endpush