@extends('layouts.app')
@section('content')
    <div class="space-y-6">
        <!-- Header Metrics Cards - Keep these -->
        <div class="col-span-12">
            <x-ecommerce.ecommerce-metrics :totalRequests="$totalRequests" :pendingRequests="$pendingRequests" :inProgressRequests="$inProgressRequests" :completedRequests="$completedRequests"
                :assignedToMe="$assignedToMe" />
        </div>

        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12 xl:col-span-7">
                <div
                    class="h-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all hover:shadow-md dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between px-5 pt-5 sm:px-6 sm:pt-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Statistics Overview</h3>
                            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Activity overview by period</p>
                        </div>
                        <div class="relative" x-data="{ open: false, selectedPeriod: 'month' }">
                            <button @click="open = !open"
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                <span
                                    x-text="selectedPeriod === 'week' ? 'Weekly' : (selectedPeriod === 'month' ? 'Monthly' : 'Yearly')"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false"
                                class="absolute right-0 mt-2 w-36 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700 z-50">
                                <div class="py-1">
                                    <button @click="selectedPeriod = 'week'; open = false; window.updateChart('week')"
                                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Weekly</button>
                                    <button @click="selectedPeriod = 'month'; open = false; window.updateChart('month')"
                                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Monthly</button>
                                    <button @click="selectedPeriod = 'year'; open = false; window.updateChart('year')"
                                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Yearly</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="max-w-full overflow-x-auto custom-scrollbar px-5 pb-5">
                        <div id="statisticsChart" class="h-80 w-full"></div>
                    </div>

                    <!-- REMOVED: Status Summary Cards - They're already in the header metrics -->
                </div>
            </div>

            <div class="col-span-12 xl:col-span-5">
                <div
                    class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white shadow-sm transition-all hover:shadow-md dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex justify-between px-5 pt-5 sm:px-6 sm:pt-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                                Priority Distribution
                            </h3>
                            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                                Requests by level
                            </p>
                        </div>
                        <x-common.dropdown-menu :items="['View Details', 'Export Data']" />
                    </div>

                    <div class="relative flex-grow flex items-center justify-center py-6">
                        <div id="priorityChart" class="h-48 w-full"></div>
                    </div>

                    <div
                        class="flex items-center justify-center gap-5 border-t border-gray-100 px-6 py-5 dark:border-gray-800 sm:gap-8">
                        <div class="text-center">
                            <p class="mb-1 text-theme-xs font-medium uppercase tracking-wider text-red-500 sm:text-xs">
                                Emergency</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white/90">
                                {{ $priorityStats->where('priority', 'emergency')->first()?->count ?? 0 }}</p>
                        </div>
                        <div class="h-8 w-px bg-gray-200 dark:bg-gray-800"></div>
                        <div class="text-center">
                            <p class="mb-1 text-theme-xs font-medium uppercase tracking-wider text-orange-500 sm:text-xs">
                                High</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white/90">
                                {{ $priorityStats->where('priority', 'high')->first()?->count ?? 0 }}</p>
                        </div>
                        <div class="h-8 w-px bg-gray-200 dark:bg-gray-800"></div>
                        <div class="text-center">
                            <p class="mb-1 text-theme-xs font-medium uppercase tracking-wider text-blue-500 sm:text-xs">
                                Medium</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white/90">
                                {{ $priorityStats->where('priority', 'medium')->first()?->count ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <x-ecommerce.recent-orders :recentRequests="$recentRequests" />
            </div>
        </div>

        @if (auth()->user()->can('maintenance_requests.assign'))
            <div class="grid grid-cols-12 gap-4 md:gap-6 mt-6">
                <!-- Issue Type Chart -->
                <div class="col-span-12 xl:col-span-6">
                    <div
                        class="h-full rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03] p-5">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 mb-3">Top Issue Types</h3>
                        <div id="issueTypeChart" class="h-72"></div>
                    </div>
                </div>

                <!-- Item Chart -->
                <div class="col-span-12 xl:col-span-6">
                    <div
                        class="h-full rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03] p-5">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 mb-3">Most Problematic Items</h3>
                        <div id="itemChart" class="h-72"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== STATUS STATISTICS CHART ==========
            @if (isset($monthlyStats) && $monthlyStats->count() > 0)
                let monthlyStatsData = @json($monthlyStats);
                let currentChart = null;

                // Define month names
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ];

                // Transform monthlyStats into the format needed for the chart
                const availableMonths = monthlyStatsData.map(item => monthNames[item.month - 1]);
                const pendingData = monthlyStatsData.map(item => item.pending || 0);
                const completedData = monthlyStatsData.map(item => item.completed || 0);
                const totalData = monthlyStatsData.map(item => item.total || 0);
                const inProgressData = monthlyStatsData.map((item, index) => {
                    return totalData[index] - (pendingData[index] + completedData[index]);
                });

                const statusColors = {
                    'pending': '#f59e0b',
                    'in_progress': '#3b82f6',
                    'completed': '#10b981'
                };

                function renderStatisticsChart() {
                    const chartOptions = {
                        series: [{
                                name: 'Pending',
                                data: pendingData,
                                color: statusColors.pending
                            },
                            {
                                name: 'In Progress',
                                data: inProgressData,
                                color: statusColors.in_progress
                            },
                            {
                                name: 'Completed',
                                data: completedData,
                                color: statusColors.completed
                            },
                            {
                                name: 'Total',
                                data: totalData,
                                color: '#6366f1',
                                type: 'line'
                            }
                        ],
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    zoom: true,
                                    pan: true,
                                    reset: true
                                }
                            },
                            stacked: false,
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 500
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 6,
                                columnWidth: '55%',
                                borderRadiusApplication: 'end'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            width: [0, 0, 0, 3],
                            curve: 'smooth',
                            dashArray: [0, 0, 0, 5]
                        },
                        xaxis: {
                            categories: availableMonths,
                            labels: {
                                style: {
                                    fontSize: '11px'
                                }
                            },
                            title: {
                                text: 'Month',
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 500
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Number of Requests',
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 500
                                }
                            },
                            min: 0,
                            forceNiceScale: true
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(val, {
                                    seriesIndex,
                                    dataPointIndex
                                }) {
                                    const seriesName = ['Pending', 'In Progress', 'Completed', 'Total'][
                                        seriesIndex
                                    ];
                                    return `${seriesName}: ${val} requests`;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'center',
                            fontSize: '11px',
                            markers: {
                                width: 10,
                                height: 10,
                                radius: 4
                            },
                            itemMargin: {
                                horizontal: 10,
                                vertical: 5
                            }
                        },
                        grid: {
                            borderColor: '#e5e7eb',
                            strokeDashArray: 5,
                            xaxis: {
                                lines: {
                                    show: false
                                }
                            }
                        },
                        responsive: [{
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 300
                                },
                                xaxis: {
                                    labels: {
                                        rotate: -45,
                                        fontSize: '10px'
                                    }
                                },
                                legend: {
                                    position: 'bottom',
                                    fontSize: '10px'
                                }
                            }
                        }]
                    };

                    if (currentChart) {
                        currentChart.destroy();
                    }

                    const chartElement = document.querySelector("#statisticsChart");
                    if (chartElement) {
                        chartElement.innerHTML = '';
                        currentChart = new ApexCharts(chartElement, chartOptions);
                        currentChart.render();
                    }
                }

                window.updateChart = function(period) {
                    renderStatisticsChart();
                };

                renderStatisticsChart();
            @else
                // Fallback: Create simple chart with current totals distributed across months
                const pendingTotal = {{ $pendingRequests }};
                const progressTotal = {{ $inProgressRequests }};
                const completedTotal = {{ $completedRequests }};
                const grandTotal = {{ $totalRequests }};

                const currentYear = new Date().getFullYear();
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ];
                const currentMonth = new Date().getMonth();

                // Distribute totals across last 6 months
                const months = [];
                const pendingData = [];
                const progressData = [];
                const completedData = [];
                const totalData = [];

                for (let i = 5; i >= 0; i--) {
                    const monthIndex = currentMonth - i;
                    const adjustedMonth = monthIndex < 0 ? monthIndex + 12 : monthIndex;
                    months.push(monthNames[adjustedMonth]);

                    pendingData.push(Math.ceil(pendingTotal / 6));
                    progressData.push(Math.ceil(progressTotal / 6));
                    completedData.push(Math.ceil(completedTotal / 6));
                    totalData.push(Math.ceil(grandTotal / 6));
                }

                const fallbackChartOptions = {
                    series: [{
                            name: 'Pending',
                            data: pendingData,
                            color: '#f59e0b'
                        },
                        {
                            name: 'In Progress',
                            data: progressData,
                            color: '#3b82f6'
                        },
                        {
                            name: 'Completed',
                            data: completedData,
                            color: '#10b981'
                        },
                        {
                            name: 'Total',
                            data: totalData,
                            color: '#6366f1',
                            type: 'line'
                        }
                    ],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: true
                        },
                        stacked: false
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '55%'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: [0, 0, 0, 3],
                        curve: 'smooth',
                        dashArray: [0, 0, 0, 5]
                    },
                    xaxis: {
                        categories: months,
                        title: {
                            text: 'Month'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Number of Requests'
                        },
                        min: 0
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'center'
                    }
                };

                const chartElement = document.querySelector("#statisticsChart");
                if (chartElement) {
                    const fallbackChart = new ApexCharts(chartElement, fallbackChartOptions);
                    fallbackChart.render();
                    window.fallbackChart = fallbackChart;
                }

                window.updateChart = function(period) {
                    if (window.fallbackChart) {
                        window.fallbackChart.render();
                    }
                };
            @endif

            // ========== PRIORITY DISTRIBUTION CHART ==========
            @if (isset($priorityStats) && $priorityStats->count() > 0)
                const priorityData = {!! $priorityStats->toJson() !!};
                const priorityLabels = priorityData.map(item => {
                    const priority = item.priority || 'unknown';
                    return priority.charAt(0).toUpperCase() + priority.slice(1);
                });
                const priorityCounts = priorityData.map(item => item.count || 0);
                const priorityColors = {
                    'Emergency': '#ef4444',
                    'High': '#f97316',
                    'Medium': '#eab308',
                    'Low': '#22c55e',
                    'Unknown': '#6b7280'
                };

                const priorityChartOptions = {
                    series: priorityCounts,
                    chart: {
                        type: 'donut',
                        height: 200,
                        fontFamily: 'Inter, sans-serif',
                        toolbar: {
                            show: false
                        }
                    },
                    labels: priorityLabels,
                    colors: priorityLabels.map(label => priorityColors[label] || '#6b7280'),
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '12px',
                                        fontWeight: 500,
                                        color: '#6b7280'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '18px',
                                        fontWeight: 600,
                                        color: '#111827'
                                    },
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        label: 'Total',
                                        fontSize: '14px',
                                        fontWeight: 500,
                                        color: '#6b7280'
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false
                    },
                    responsive: [{
                        breakpoint: 640,
                        options: {
                            chart: {
                                height: 180
                            }
                        }
                    }],
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.1
                            }
                        }
                    }
                };

                const priorityChart = new ApexCharts(document.querySelector("#priorityChart"),
                    priorityChartOptions);
                priorityChart.render();
            @endif

            // ========== ISSUE TYPE CHART ==========
            @if (auth()->user()->can('maintenance_requests.assign'))
                function aggregateTopItems(data, limit = 10) {
                    if (!data || data.length === 0) return [];

                    const filteredData = data.filter(item => {
                        const count = item.maintenance_requests_count || item.count || 0;
                        return count > 1;
                    });

                    if (filteredData.length === 0) return [];

                    const sorted = [...filteredData].sort((a, b) => {
                        const countA = a.maintenance_requests_count || a.count || 0;
                        const countB = b.maintenance_requests_count || b.count || 0;
                        return countB - countA;
                    });

                    const topItems = sorted.slice(0, limit);
                    const otherItems = sorted.slice(limit);

                    if (otherItems.length > 0) {
                        const otherCount = otherItems.reduce((sum, item) => sum + (item
                            .maintenance_requests_count || item.count || 0), 0);
                        topItems.push({
                            name: `Others (${otherItems.length} items)`,
                            maintenance_requests_count: otherCount
                        });
                    }

                    return topItems;
                }

                @if ($issueTypes > 0 && isset($issueTypeStats) && $issueTypeStats->count() > 0)
                    const rawIssueData = {!! $issueTypeStats->toJson() !!};
                    const aggregatedIssues = aggregateTopItems(rawIssueData, 12);

                    if (aggregatedIssues.length > 0) {
                        const issueTypeLabels = aggregatedIssues.map(item => {
                            let label = item.name || 'Unknown';
                            return label.length > 30 ? label.substring(0, 27) + '...' : label;
                        });
                        const issueTypeCounts = aggregatedIssues.map(item => item.maintenance_requests_count || 0);
                        const issueColors = issueTypeLabels.map((_, i) => `hsl(${(i * 30) % 360}, 70%, 50%)`);

                        const issueTypeChartOptions = {
                            series: [{
                                data: issueTypeCounts
                            }],
                            chart: {
                                type: 'bar',
                                height: Math.min(500, Math.max(300, issueTypeLabels.length * 35)),
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true,
                                        zoom: true,
                                        pan: true
                                    }
                                }
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 4,
                                    horizontal: true,
                                    distributed: true,
                                    barHeight: '70%',
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: (val) => val + ' reqs',
                                offsetX: 10,
                                style: {
                                    fontSize: '11px',
                                    colors: ['#333']
                                }
                            },
                            xaxis: {
                                categories: issueTypeLabels,
                                labels: {
                                    style: {
                                        fontSize: '11px'
                                    },
                                    rotate: 0,
                                    trim: true
                                },
                                title: {
                                    text: 'Number of Requests'
                                }
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        fontSize: '11px'
                                    },
                                    formatter: (val) => val.length > 25 ? val.substring(0, 22) + '...' : val
                                }
                            },
                            colors: issueColors,
                            title: {
                                text: 'Top Issue Types',
                                align: 'left',
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold'
                                }
                            }
                        };

                        const issueTypeChart = new ApexCharts(document.querySelector("#issueTypeChart"),
                            issueTypeChartOptions);
                        issueTypeChart.render();
                    } else {
                        document.getElementById('issueTypeChart').innerHTML =
                            '<div class="flex items-center justify-center h-64 text-gray-500">No issue types with >1 request</div>';
                    }
                @else
                    document.getElementById('issueTypeChart').innerHTML =
                        '<div class="flex items-center justify-center h-64 text-gray-500">No issue type data available</div>';
                @endif

                // ========== ITEM CHART ==========
                @if (isset($itemAnalysis) && $itemAnalysis->count() > 0)
                    const rawItemData = {!! $itemAnalysis->toJson() !!};
                    const aggregatedItems = aggregateTopItems(rawItemData, 15);

                    if (aggregatedItems.length > 0) {
                        const treemapData = aggregatedItems.map(item => ({
                            x: (item.name || 'Unknown').length > 35 ? (item.name || 'Unknown')
                                .substring(0, 32) + '...' : (item.name || 'Unknown'),
                            y: item.maintenance_requests_count || item.count || 0
                        }));

                        const itemChartOptions = {
                            series: [{
                                data: treemapData
                            }],
                            chart: {
                                type: 'treemap',
                                height: 400,
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true
                                    }
                                }
                            },
                            plotOptions: {
                                treemap: {
                                    distributed: true,
                                    enableShades: true,
                                    shadeIntensity: 0.5,
                                    colorScale: {
                                        ranges: [{
                                                from: 2,
                                                to: 10,
                                                color: '#86efac'
                                            },
                                            {
                                                from: 11,
                                                to: 50,
                                                color: '#4ade80'
                                            },
                                            {
                                                from: 51,
                                                to: 100,
                                                color: '#22c55e'
                                            },
                                            {
                                                from: 101,
                                                to: 200,
                                                color: '#16a34a'
                                            },
                                            {
                                                from: 201,
                                                to: 500,
                                                color: '#15803d'
                                            },
                                            {
                                                from: 501,
                                                to: 1000,
                                                color: '#166534'
                                            }
                                        ]
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '11px',
                                    colors: ['#fff']
                                },
                                formatter: (text, op) => [text, op.value],
                                offsetY: -2
                            },
                            tooltip: {
                                y: {
                                    formatter: (value, {
                                        dataPointIndex
                                    }) => `${treemapData[dataPointIndex].x}: ${value} requests`
                                }
                            },
                            title: {
                                text: 'Problematic Items Distribution',
                                align: 'left',
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold'
                                }
                            }
                        };

                        const itemChart = new ApexCharts(document.querySelector("#itemChart"), itemChartOptions);
                        itemChart.render();
                    } else {
                        document.getElementById('itemChart').innerHTML =
                            '<div class="flex items-center justify-center h-64 text-gray-500">No items with >1 request</div>';
                    }
                @else
                    document.getElementById('itemChart').innerHTML =
                        '<div class="flex items-center justify-center h-64 text-gray-500">No item data available</div>';
                @endif
            @endif
        });
    </script>
@endpush
