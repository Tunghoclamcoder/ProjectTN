<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý biểu đồ')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />

    <!-- ========== All CSS files linkup ========= -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lineicons.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/materialdesignicons.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
    <script src="{{ asset('js/alert.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    @include('management.components.admin-header')

    <div class="container mt-3 mb-4 ml-0">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary d-inline-flex align-items-center shadow-sm"
            style="font-weight: 500; font-size: 1.05rem;">
            <i class="lni lni-arrow-left me-2" style="font-size: 1.2em;"></i>
            Dashboard
        </a>
    </div>

    <div class="alerts-container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>
    <div class="row">
        <!-- End Col -->
        <div class="col-lg-5">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap justify-content-between">
                    <div class="left">
                        <h6 class="text-medium mb-10">Thống kê doanh thu</h6>
                        <h3 class="text-bold">{{ number_format($totalRevenue) }}đ</h3>
                    </div>
                    <div class="right">
                        <div class="select-style-1">
                            <div class="select-position select-sm">
                                <select class="light-bg" id="revenueFilter">
                                    <option value="7days" selected>7 ngày qua</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chart">
                    <canvas id="Chart2" style="width: 100%; height: 400px;"></canvas>
                </div>
            </div>


        </div>
        <!-- End Col -->

        <!-- End Col -->
        <div class="col-lg-7">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap align-items-center justify-content-between">
                    <div class="left">
                        <h6 class="text-medium mb-2">Thống kê đơn hàng đã hoàn thành và bị hủy</h6>
                    </div>
                    <div class="right">
                        <div class="select-style-1 mb-2">
                            <div class="select-position select-sm">
                                <select class="bg-ligh">
                                    <option value="">Last 7 days</option>
                                </select>
                            </div>
                        </div>
                        <!-- end select -->
                    </div>
                </div>
                <!-- End Title -->
                <div class="chart">
                    <div id="legend4">
                        <ul class="legend3 d-flex flex-wrap gap-3 gap-sm-0 align-items-center mb-30">
                            <li>
                                <div class="d-flex">
                                    <span class="bg-color primary-bg"></span>
                                    <div class="text">
                                        <p class="text-sm {{ $completedTrend >= 0 ? 'text-success' : 'text-danger' }}">
                                            <span class="text-dark">Đơn hoàn thành</span>
                                            {{ $completedTrend >= 0 ? '+' : '' }}{{ $completedTrend }}%
                                            <i
                                                class="lni {{ $completedTrend >= 0 ? 'lni-arrow-up' : 'lni-arrow-down' }}"></i>
                                        </p>
                                        <h2>{{ $completedThisWeek }}</h2>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <span class="bg-color danger-bg"></span>
                                    <div class="text">
                                        <p class="text-sm {{ $canceledTrend >= 0 ? 'text-danger' : 'text-success' }}">
                                            <span class="text-dark">Đơn hủy</span>
                                            {{ $canceledTrend >= 0 ? '+' : '' }}{{ $canceledTrend }}%
                                            <i
                                                class="lni {{ $canceledTrend >= 0 ? 'lni-arrow-up' : 'lni-arrow-down' }}"></i>
                                        </p>
                                        <h2>{{ $canceledThisWeek }}</h2>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <canvas id="Chart4" style="width: 100%; height: 420px; margin-left: -35px;"></canvas>
                </div>
                <!-- End Chart -->
            </div>
        </div>
        <!-- End Col -->
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <h6 class="mb-10">Top thương hiệu bán chạy</h6>
                <div id="chartContainer" style="position: relative; height: 400px;">
                    <canvas id="brandChart"></canvas>
                    <div id="chartError" class="text-center text-danger mt-3" style="display: none;">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap justify-content-between">
                    <div class="left">
                        <h6 class="text-medium mb-10">Tỉ lệ sử dụng voucher 7 ngày gần nhất</h6>
                        <h3 class="text-bold">Tổng lượt sử dụng: {{ array_sum($voucherUsageCounts) }}</h3>
                    </div>
                    <div class="right">
                        <div class="select-style-1">
                            <div class="select-position select-sm">
                                <select class="light-bg">
                                    <option value="" selected>7 ngày qua</option>
                                </select>
                            </div>
                        </div>
                        <!-- end select -->
                    </div>
                </div>
                <!-- End Title -->
                <div class="chart">
                    <canvas id="Chart1" style="width: 100%; height: 400px; margin-left: -35px;"></canvas>
                </div>
                <!-- End Chart -->
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Biểu đồ cột ngang: Sản phẩm bán ra theo danh mục -->
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <h6 class="mb-10">Số lượng sản phẩm bán ra theo danh mục</h6>
                <div class="chart">
                    <canvas id="categoryBarChart" style="width: 100%; height: 400px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ cột dọc: Sản phẩm bán ra theo thương hiệu -->
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <h6 class="mb-10">Số lượng sản phẩm bán ra theo thương hiệu</h6>
                <div class="chart">
                    <canvas id="brandBarChart" style="width: 100%; height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

</body>
<script>
    const ctx1 = document.getElementById("Chart1").getContext("2d");
    const chart1 = new Chart(ctx1, {
        type: "line",
        data: {
            labels: @json($voucherChartLabels),
            datasets: [{
                label: "Số đơn sử dụng voucher",
                backgroundColor: "transparent",
                borderColor: "#365CF5",
                data: @json($voucherUsageCounts),
                pointBackgroundColor: "transparent",
                pointHoverBackgroundColor: "#365CF5",
                pointBorderColor: "transparent",
                pointHoverBorderColor: "#fff",
                pointHoverBorderWidth: 5,
                borderWidth: 5,
                pointRadius: 8,
                pointHoverRadius: 8,
                cubicInterpolationMode: "monotone",
            }],
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Số đơn sử dụng voucher: ' + context.raw;
                        },
                        labelColor: function(context) {
                            return {
                                backgroundColor: "#ffffff",
                                color: "#171717"
                            };
                        },
                    },
                    intersect: false,
                    backgroundColor: "#f9f9f9",
                    title: {
                        fontFamily: "Plus Jakarta Sans",
                        color: "#8F92A1",
                        fontSize: 12,
                    },
                    body: {
                        fontFamily: "Plus Jakarta Sans",
                        color: "#171717",
                        fontStyle: "bold",
                        fontSize: 16,
                    },
                    multiKeyBackground: "transparent",
                    displayColors: false,
                    padding: {
                        x: 30,
                        y: 10,
                    },
                    bodyAlign: "center",
                    titleAlign: "center",
                    titleColor: "#8F92A1",
                    bodyColor: "#171717",
                    bodyFont: {
                        family: "Plus Jakarta Sans",
                        size: "16",
                        weight: "bold",
                    },
                },
                legend: {
                    display: false,
                },
            },
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: false,
            },
            scales: {
                y: {
                    grid: {
                        display: false,
                        drawTicks: false,
                        drawBorder: false,
                    },
                    ticks: {
                        padding: 35,
                        min: 0,
                    },
                },
                x: {
                    grid: {
                        drawBorder: false,
                        color: "rgba(143, 146, 161, .1)",
                        zeroLineColor: "rgba(143, 146, 161, .1)",
                    },
                    ticks: {
                        padding: 20,
                    },
                },
            },
        },
    });

    const ctx2 = document.getElementById("Chart2").getContext("2d");
    const chart2 = new Chart(ctx2, {
        type: "bar",
        data: {
            labels: @json($last7Days),
            datasets: [{
                label: "Doanh thu",
                backgroundColor: "#365CF5",
                borderRadius: 30,
                barThickness: 6,
                maxBarThickness: 8,
                data: @json($dailyRevenue),
            }],
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(context.raw);
                        }
                    },
                    backgroundColor: "#F3F6F8",
                    titleAlign: "center",
                    bodyAlign: "center",
                    titleFont: {
                        size: 12,
                        weight: "bold",
                        color: "#8F92A1",
                    },
                    bodyFont: {
                        size: 16,
                        weight: "bold",
                        color: "#171717",
                    },
                    displayColors: false,
                    padding: {
                        x: 30,
                        y: 10,
                    },
                },
                legend: {
                    display: false,
                }
            },
            layout: {
                padding: {
                    top: 15,
                    right: 15,
                    bottom: 15,
                    left: 15,
                },
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    grid: {
                        display: false,
                        drawTicks: false,
                        drawBorder: false,
                    },
                    ticks: {
                        padding: 35,
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        }
                    },
                    min: 0,
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                        color: "rgba(143, 146, 161, .1)",
                        drawTicks: false,
                        zeroLineColor: "rgba(143, 146, 161, .1)",
                    },
                    ticks: {
                        padding: 20,
                    }
                }
            }
        }
    });
    // =========== chart two end

    // =========== chart three start
    document.addEventListener('DOMContentLoaded', function() {
        const chartContainer = document.getElementById('chartContainer');
        const canvas = document.getElementById('brandChart');
        const errorDiv = document.getElementById('chartError');

        fetch('{{ route('admin.dashboard.brandStats') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }
                return data;
            })
            .then(result => {
                if (!result.success) {
                    throw new Error(result.message || 'Không có dữ liệu');
                }

                const data = result.data;
                if (!data || data.length === 0) {
                    throw new Error('Không có dữ liệu thương hiệu');
                }

                // Create chart with data
                new Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels: data.map(item => item.brand_name),
                        datasets: [{
                            data: data.map(item => item.percentage),
                            backgroundColor: [
                                '#8901dc',
                                '#01dc8c',
                                '#ebf15b',
                                '#ADC2FD',
                                '#0139dc'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const item = data[context.dataIndex];
                                        return `${item.brand_name}: ${item.percentage}% (${item.total_sales} đơn)`;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = `
            <div class="alert alert-danger">
                <p>Không thể tải dữ liệu biểu đồ</p>
                <small>${error.message}</small>
            </div>
        `;
            });
    });
    // =========== chart three end

    // ================== chart four start
    const ctx4 = document.getElementById("Chart4").getContext("2d");
    const chart4 = new Chart(ctx4, {
        type: "bar",
        data: {
            labels: @json($orderChartLabels),
            datasets: [{
                    label: "Đơn hoàn thành",
                    backgroundColor: "#365CF5",
                    borderColor: "transparent",
                    borderRadius: 20,
                    borderWidth: 5,
                    barThickness: 20,
                    maxBarThickness: 20,
                    data: @json($completedCounts),
                },
                {
                    label: "Đơn hủy",
                    backgroundColor: "#d50100",
                    borderColor: "transparent",
                    borderRadius: 20,
                    borderWidth: 5,
                    barThickness: 20,
                    maxBarThickness: 20,
                    data: @json($canceledCounts),
                },
            ],
        },
        options: {
            plugins: {
                tooltip: {
                    backgroundColor: "#F3F6F8",
                    titleColor: "#8F92A1",
                    titleFontSize: 12,
                    bodyColor: "#171717",
                    bodyFont: {
                        weight: "bold",
                        size: 16,
                    },
                    multiKeyBackground: "transparent",
                    displayColors: true,
                    padding: {
                        x: 30,
                        y: 10,
                    },
                    bodyAlign: "center",
                    titleAlign: "center",
                    enabled: true,
                },
                legend: {
                    display: true,
                },
            },
            layout: {
                padding: {
                    top: 0,
                },
            },
            responsive: true,
            // maintainAspectRatio: false,
            title: {
                display: false,
            },
            scales: {
                y: {
                    grid: {
                        display: false,
                        drawTicks: false,
                        drawBorder: false,
                    },
                    ticks: {
                        padding: 35,
                        min: 0,
                    },
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                        color: "rgba(143, 146, 161, .1)",
                        zeroLineColor: "rgba(143, 146, 161, .1)",
                    },
                    ticks: {
                        padding: 20,
                    },
                },
            },
        },
    });
    // =========== chart four end

    // Biểu đồ cột ngang: Danh mục
    const ctxCategory = document.getElementById("categoryBarChart").getContext("2d");
    new Chart(ctxCategory, {
        type: "bar",
        data: {
            labels: @json($categoryNames),
            datasets: [{
                label: "Số lượng bán ra",
                data: @json($categoryTotals),
                backgroundColor: "#365CF5",
                borderRadius: 10,
                barThickness: 18,
                // Thêm nếu muốn: minBarLength: 2,
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    // Thêm 2 dòng sau để giãn cột
                    barPercentage: 0.5,
                    categoryPercentage: 0.5
                },
                y: {
                    beginAtZero: true,
                    barPercentage: 0.5,
                    categoryPercentage: 0.5
                }
            }
        }
    });

    // Biểu đồ cột dọc: Thương hiệu
    const ctxBrand = document.getElementById("brandBarChart").getContext("2d");
    new Chart(ctxBrand, {
        type: "bar",
        data: {
            labels: @json($brandNames),
            datasets: [{
                label: "Số lượng bán ra",
                data: @json($brandTotals),
                backgroundColor: "#01dc8c",
                borderRadius: 10,
                barThickness: 18,
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    barPercentage: 0.5,
                    categoryPercentage: 0.5
                },
                x: {
                    beginAtZero: true,
                    barPercentage: 0.5,
                    categoryPercentage: 0.5
                }
            }
        }
    });
</script>

</html>
