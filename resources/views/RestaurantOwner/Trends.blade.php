@extends('Model/restaurantOwnerModel')

@section('title', 'Trends')

@section('content')

<div id="container">
    <div id="view-dashboard">
        <h1>Trends</h1>
        <div class="card">
            <div class="card-body">
                Trends
                <select name="duration" id="duration">
                    <option value="today" {{ request()->get('duration') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request()->get('duration') == 'week' ? 'selected' : '' }}>Last week</option>
                    <option value="month" {{ request()->get('duration') == 'month' ? 'selected' : '' }}>Last month</option>
                    <option value="3months" {{ request()->get('duration') == '3months' ? 'selected' : '' }}>Last 3 months</option>
                    <option value="6months" {{ request()->get('duration') == '6months' ? 'selected' : '' }}>Last 6 months</option>
                    <option value="year" {{ request()->get('duration') == 'year' ? 'selected' : '' }}>Last year</option>
                    <option value="lifetime" {{ request()->get('duration') == 'lifetime' ? 'selected' : '' }}>Entire Lifetime</option>
                </select>

                <br>
                <div class="bar-chart">
                    <canvas id="chart">

                    </canvas>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                Customer Leads Breakdown for Given Duration
                <div class="row">
                    <div class="col-md-4">
                        <div class="breakdown-box">
                            <div class="icon"><i class="fa fa-4x fa-search"></i></div>
                            <div class="count">{{ $stats['search'] }}</div>
                            <div class="tag">Views</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="breakdown-box">
                            <div class="icon"><i class="fa fa-4x fa-star"></i></div>
                            <div class="count">{{ $stats['rating'] }}</div>
                            <div class="tag">Rating</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="breakdown-box">
                            <div class="icon"><i class="fa fa-4x fa-pen"></i></div>
                            <div class="count">{{ $stats['review'] }}</div>
                            <div class="tag">Reviews</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="breakdown-box">
                            <div class="icon"><i class="fa fa-4x fa-bookmark"></i></div>
                            <div class="count">{{ $stats['bookmark'] }}</div>
                            <div class="tag">Bookmarks</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="breakdown-box">
                            <div class="icon"><i class="fa fa-4x fa-map-marker-alt"></i></div>
                            <div class="count">{{ $stats['mapdirection'] }}</div>
                            <div class="tag">Map Direction</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="breakdown-box">
                            <div class="icon"><i class="fa fa-4x fa-phone"></i></div>
                            <div class="count">{{ $stats['mobilecall'] }}</div>
                            <div class="tag">Mobile Calls</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra-css')
    <style>
        .breakdown-box  {
            border: 2px solid #ccc;
            box-shadow: 0 0 5px #ccc;
            margin:50px;
            text-align: center;
            padding:50px 0;
        }

        .breakdown-box:hover {
            box-shadow: 0 0 10px #ddd;
        }

        .breakdown-box .count {
            font-size:25px;
            font-weight:bold;
            line-height:35px;
            margin-top:5px;
        }
          
        .breakdown-box .tag {
            color:#777;
            font-size:30px;
            margin-top:10px;
        }

        .bar-chart {
            width:70%;
            height:auto;
            margin:auto;
        }
    </style> 
@endsection

@section('extra-js')
    <!-- Inline JS -->
    <script>
        $('li').removeClass('active');
        $('#menu-trends').parent().addClass('active');

        // Duration Changed
        $('#duration').on('change', function(e){
            window.location.href = window.location.href.split('?')[0] + '?duration=' + $(this).val();
        });


        // Create chart
        var ctx = document.getElementById('chart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! $totalheads->pluck('needle')->toJson() !!},
                datasets: [{
                    label: 'Total Heads',
                    data: [{{ $totalheads->implode('count', ',') }}],
                    backgroundColor:'#019be1',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
@endsection