@extends('master')
@section('title', 'Sales Report')
@section('content')
    <div class="panel panel-info">
        <div class="panel-heading">
            <h2>
                <i class="fa fa-area-chart" aria-hidden="true"></i>
                Sales Report
                <a href="/admin/store/report/inventory" class="btn btn-success btn-raised pull-right">Inventory Report</a>
                <a href="/admin/store/report/sales/export?start={{ $filters['start'] }}&end={{ $filters['end'] }}&store={{ $filters['store'] }}&type=detailed" class="btn btn-info btn-raised pull-right" style="margin-right:10px;">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export to Excel
                </a>
            </h2>
        </div>
        @include('backend.store.report.partials.sales-filters')
    </div>
    <!-- Start Sales Details -->
    <div class="row">
        <div class="col-md-3">
            <div class="well">
                <table class="table">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center">
                                <h3><strong>Total Sales</strong></h3>
                            </td>
                        </tr>
                        <tr>
                            <td><h4>Gross Sales</h4></td>
                            <td><h4>${{ $data['gross'] }}</h4></td>
                        </tr>
                        <tr>
                            <td><h4>Net Sales</h4></td>
                            <td><h4>${{ $data['net'] }}</h4></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="well">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center">
                                <strong>Sales Breakdown</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Cash Sales</td>
                            <td>${{ $data['cash'] }}</td>
                        </tr>
                        <tr>
                            <td>Credit Sales</td>
                            <td>${{ $data['credit'] }}</td>
                        </tr>
                        <tr>
                            <td>Product Sales</td>
                            <td>${{ $data['productSales'] }}</td>
                        </tr>
                        <tr>
                            <td>Liquid Sales</td>
                            <td>${{ $data['liquidSales'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="well">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center">
                                <strong>Averages & Tax</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Total Orders</td>
                            <td>{{ $data['totalOrders'] }}</td>
                        </tr>
                        <tr>
                            <td>Average Order</td>
                            <td>${{ number_format($data['averageOrder'],2) }}</td>
                        </tr>
                        <tr>
                            <td>Sales Tax</td>
                            <td>${{ number_format($data['subtotal'] * config('store.tax'),2) }}</td>
                        </tr>
                        <tr>
                            <td>Discounts Given</td>
                            <td>-${{ number_format($data['discounts'],2) }}</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="well">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center"><strong>Bottles Sold</strong></td>
                        </tr>
                        @foreach($data['liquids'] as $size => $numberSold)
                            <tr>
                                <td>{{ $size }}ml</td>
                                <td>{{ $numberSold }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>Total ml</td>
                            <td>{{ $data['totalMl'] }}ml</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Sales Details -->

    <!-- Start Charts -->
    <div class="row">
        <div class="col-md-4">
            <div class="well">
                <h2 class="text-center">Employee Sales</h2>
                <canvas id="employeeSales" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="well">
                <h2 class="text-center">Hourly Sales</h2>
                <canvas id="hourlySales" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="well">
                <h2 class="text-center">Customers Per Hour</h2>
                <canvas id="hourlyCustomers" height="200"></canvas>
            </div>
        </div>
    </div>
    <!-- End Charts -->

    <div class="row">
        <div class="col-md-4">
            <div class="well text-center">
                <button id="employeeDetailsToggle" class="btn btn-raised btn-info">Show Table</button>
                <table id="employeeDetails" class="table table-striped" style="display:none">
                    <tbody>
                    @foreach($data['employee'] as $name => $sales)
                        <tr>
                            <td class="name">{{ $name }}</td>
                            <td class="esale">{{ $sales }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="well text-center">
                <button id="hourlyDetailsToggle" class="btn btn-raised btn-info">Show Table</button>
                <table id="hourlyDetails" class="table table-striped" style="display:none">
                    <tbody>
                        @foreach($data['hourly'] as $hour => $sales)
                            <tr>
                                <td class="hour">{{ $hour }}</td>
                                <td class="hsale">{{ $sales }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="well text-center">
                <button id="customerDetailsToggle" class="btn btn-raised btn-info">Show Table</button>
                <table id="customerDetails" class="table table-striped" style="display:none">
                    <tbody>
                    @foreach($data['customers'] as $hour => $customers)
                        <tr>
                            <td class="chour">{{ $hour }}</td>
                            <td class="customers">{{ $customers }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script src="/js/chart.min.js"></script>
<script>
    $(document).ready(function() {
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });

    $('#filter').on('click', function() {

        var start = "start=" + $('#start').val();
        var end = "end=" + $('#end').val();
        var store = "store=" + $('#store-select :selected').val();
        var type = "type=" + $('#report-type :selected').val();
        var url = "/admin/store/report/sales?" + start + '&' + end + '&' + store + '&' + type;
        window.location.href = url;
    });

    $('#hourlyDetailsToggle').on('click', function() {
        $('#hourlyDetails').fadeToggle();
    });

    var salesLabels = [];
    var salesValues = [];
    $('.hour').each(function() {
        salesLabels.push($(this).html());
    });
    $('.hsale').each(function() {
        salesValues.push($(this).html());
    });

    var salesChart = new Chart($('#hourlySales'), {
        type: 'doughnut',
        data: {
            labels: salesLabels,
            datasets: [{
                label: '# of Votes',
                data: salesValues,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(39, 150, 74, 0.2)',
                    'rgba(39, 130, 176, 0.2)',
                    'rgba(50, 39, 176, 0.2)',
                    'rgba(141, 39, 176, 0.2)',
                    'rgba(187, 56, 27, 0.2)',
                    'rgba(187, 27, 46, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(39, 150, 74, 1)',
                    'rgba(39, 130, 176, 1)',
                    'rgba(50, 39, 176, 1)',
                    'rgba(141, 39, 176, 1)',
                    'rgba(187, 56, 27, 1)',
                    'rgba(187, 27, 46, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {

        }
    });

    $('#customerDetailsToggle').on('click', function() {
        $('#customerDetails').fadeToggle();
    });

    var customerLabels = [];
    var customerValues = [];
    $('.chour').each(function() {
        customerLabels.push($(this).html());
    });
    $('.customers').each(function() {
        customerValues.push($(this).html());
    });
    var hourlyCustomers = new Chart($('#hourlyCustomers'), {
        type: 'doughnut',
        data: {
            labels: customerLabels,
            datasets: [{
                label: '# of Votes',
                data: customerValues,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(39, 150, 74, 0.2)',
                    'rgba(39, 130, 176, 0.2)',
                    'rgba(50, 39, 176, 0.2)',
                    'rgba(141, 39, 176, 0.2)',
                    'rgba(187, 56, 27, 0.2)',
                    'rgba(187, 27, 46, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(39, 150, 74, 1)',
                    'rgba(39, 130, 176, 1)',
                    'rgba(50, 39, 176, 1)',
                    'rgba(141, 39, 176, 1)',
                    'rgba(187, 56, 27, 1)',
                    'rgba(187, 27, 46, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {

        }
    });

    $('#employeeDetailsToggle').on('click', function() {
        $('#employeeDetails').fadeToggle();
    });

    var employees = [];
    var esales = [];
    $('.name').each(function() {
        employees.push($(this).html());
    });
    $('.esale').each(function() {
        esales.push($(this).html());
    });
    var employeeSales = new Chart($('#employeeSales'), {
        type: 'doughnut',
        data: {
            labels: employees,
            datasets: [{
                label: '# of Votes',
                data: esales,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(39, 150, 74, 0.2)',
                    'rgba(39, 130, 176, 0.2)',
                    'rgba(50, 39, 176, 0.2)',
                    'rgba(141, 39, 176, 0.2)',
                    'rgba(187, 56, 27, 0.2)',
                    'rgba(187, 27, 46, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(39, 150, 74, 1)',
                    'rgba(39, 130, 176, 1)',
                    'rgba(50, 39, 176, 1)',
                    'rgba(141, 39, 176, 1)',
                    'rgba(187, 56, 27, 1)',
                    'rgba(187, 27, 46, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {

        }
    });
</script>
@endpush