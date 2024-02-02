@extends('layouts.main')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalMessages }}</h3>
                            <p>Total Messages <br>(All Time)</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-chatbubbles"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $totalChatTime }} mins<sup style="font-size: 20px"></sup></h3>
                            <p>Total Chat Time <br>(All Time)</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $totalMessagesLatestMonth }}</h3>
                            <p>Total Messages <br>(Latest Month)</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-chatbubble-working"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $totalChatTimeLatestMonth }} mins</h3>
                            <p>Total Chat Time <br>(Latest Month)</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->

            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-12 connectedSortable">
                    <!-- Custom tabs (Charts with tabs) -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Overview
                            </h3>
                            <div class="card-tools">
                                <ul class="nav nav-pills ml-auto">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Total Messages by
                                            Week (Latest Month)</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#sales-chart" data-toggle="tab">Favourite Tags</a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content p-0">
                                <!-- Area Chart - Total Messages by Week -->
                                <div class="tab-pane active" id="revenue-chart">
                                    <div class="chart-container">
                                        <canvas id="messagesByWeekChart" height="300" style="height: 300px;"></canvas>
                                    </div>
                                </div>
                                <!-- Donut Chart - Top 5 Favourite Tags -->
                                <div class="tab-pane" id="sales-chart">
                                    <div class="chart-container">
                                        <canvas id="favouriteTagsChart" height="300" style="height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </section>
                <!-- /.Left col -->
            </div>
            <!-- /.row (main row) -->

            <!-- Additional content -->
            <section class="col-lg-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @if (session('status'))
                            <p>{{ session('status') }}</p>
                        @endif

                        <!-- Display total messages and total chat time for all time -->
                        <p>Total Messages (All Time): {{ $totalMessages }}</p>
                        <p>Total Chat Time (All Time): {{ $totalChatTime }} minutes</p>

                        <!-- Display total message and total chat time for the latest month -->
                        <p>Total Messages (Latest Month): {{ $totalMessagesLatestMonth }}</p>
                        <p>Total Chat Time (Latest Month): {{ $totalChatTimeLatestMonth }} minutes</p>
                        <br>

                        <!-- Display total messages by weeks for the latest month -->
                        <p>Total Messages by Weeks (Latest Month):</p>
                        <ul>
                            @foreach ($totalMessagesByWeeks as $week => $count)
                                <li>Week {{ $week }}: {{ $count }} messages</li>
                            @endforeach
                        </ul>
                        <br>

                        <!-- Display favourite tags -->
                        <p>Favourite Tags:</p>
                        <ul>
                            @php
                                // Sort the tags by count in descending order
                                arsort($topFavouriteTags);

                                // Take only the top 5 tags
                                $top5FavouriteTags = array_slice($topFavouriteTags, 0, 5);
                            @endphp
                            @foreach ($topFavouriteTags as $tag => $count)
                                <li>{{ $tag }} (Used {{ $count }} times)</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
            <!-- /.Additional content -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Your HTML content -->

    <script>
        // Total Messages by Week Chart
        var messagesByWeekData = {
            labels: {!! json_encode(
                array_map(
                    function ($week) {
                        return 'w' . $week;
                    },
                    is_array($totalMessagesByWeeks) ? array_keys($totalMessagesByWeeks) : array_keys($totalMessagesByWeeks->all()),
                ),
            ) !!},
            datasets: [{
                label: 'Total Messages',
                data: {!! json_encode(
                    is_array($totalMessagesByWeeks) ? array_values($totalMessagesByWeeks) : array_values($totalMessagesByWeeks->all()),
                ) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        var messagesByWeekCtx = document.getElementById('messagesByWeekChart').getContext('2d');
        var messagesByWeekChart = new Chart(messagesByWeekCtx, {
            type: 'bar',
            data: messagesByWeekData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Top Favourite Tags Chart
        var favouriteTagsData = {
            labels: {!! json_encode(array_keys($topFavouriteTags)) !!},
            datasets: [{
                data: {!! json_encode(array_values($topFavouriteTags)) !!},
                backgroundColor: [
                    'red', 'blue', 'green', 'yellow', 'purple',
                    'orange', 'cyan', 'pink', 'lime', 'maron',
                    'navy', 'magenta', 'olive', 'gray', 'lightgray' // Replace 'light gray' with 'lightgray'
                ]
            }]
        };

        var favouriteTagsCtx = document.getElementById('favouriteTagsChart').getContext('2d');
        var favouriteTagsChart = new Chart(favouriteTagsCtx, {
            type: 'doughnut',
            data: favouriteTagsData,
            options: {
                legend: {
                    display: false // Hide the legend to ensure all tags are visible
                }
            }
        });
    </script>
@endsection
