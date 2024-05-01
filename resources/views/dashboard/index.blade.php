@extends('layouts.app')
@push('css')
<link href="{{ pathAssets('vendor/chartist/css/chartist.min.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="row page-titles trust-wave mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text ">
            <h4>Hi, welcome back!</h4>
            <p class="mb-0">Your business dashboard template</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-sm-6">
        <div class="card">
            <div class="stat-widget-one card-body">
                <div class="stat-icon d-inline-block">
                    <i class="ti-money text-success border-success"></i>
                </div>
                <div class="stat-content d-inline-block">
                    <div class="stat-text">Total Visits</div>
                    <div class="stat-digit">{{ totalVisit() }} </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card">
            <div class="stat-widget-one card-body">
                <div class="stat-icon d-inline-block">
                    <i class="ti-user text-primary border-primary"></i>
                </div>
                <div class="stat-content d-inline-block">
                    <div class="stat-text">Completed Visit</div>
                    <div class="stat-digit">{{ completedVisit() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card">
            <div class="stat-widget-one card-body">
                <div class="stat-icon d-inline-block">
                    <i class="ti-layout-grid2 text-pink border-pink"></i>
                </div>
                <div class="stat-content d-inline-block">
                    <div class="stat-text">Pending Visit</div>
                    <div class="stat-digit">{{ pendingVisit() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card">
            <div class="stat-widget-one card-body">
                <div class="stat-icon d-inline-block">
                    <i class="ti-link text-danger border-danger"></i>
                </div>
                <div class="stat-content d-inline-block">
                    <div class="stat-text">Total Team</div>
                    <div class="stat-digit">{{ totalTeam() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Fee Collections and Expenses</h4>
            </div>
            <div class="card-body">
                <div class="ct-bar-chart mt-5"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="ct-pie-chart"></div>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
<script src="{{ pathAssets('vendor/chartist/js/chartist.min.js') }}"></script>
@endpush
@push('script')
    <script>
        //  pie chart
         var chartData = {!! pieChart() !!};
         console.log(chartData);


         var options = {
            labelInterpolationFnc: function(value) {
                return value[0]
            }
        };

        var responsiveOptions = [
            ['screen and (min-width: 640px)', {
                chartPadding: 30,
                labelOffset: 100,
                labelDirection: 'explode',
                labelInterpolationFnc: function(value) {
                    return value;
                }
            }],
            ['screen and (min-width: 1024px)', {
                labelOffset: 80,
                chartPadding: 20
            }]
        ];

        new Chartist.Pie('.ct-pie-chart', chartData, options, responsiveOptions);

            // bar chart
        var barChart = {!! barChart() !!}
        var options = {
        seriesBarDistance: 10
    };

    var responsiveOptions = [
        ['screen and (max-width: 640px)', {
            seriesBarDistance: 5,
            axisX: {
                labelInterpolationFnc: function(value) {
                    return value[0];
                }
            }
        }]
    ];

    new Chartist.Bar('.ct-bar-chart', barChart, options, responsiveOptions);
    </script>
@endpush
