@extends('layouts.app')
@section('content')
    <div class="space-y-6">
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
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                                Monthly Statistics
                            </h3>
                            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Activity overview per month</p>
                        </div>
                        <x-common.dropdown-menu :items="['Last 7 days', 'Last 30 days', 'Last 90 days']" />
                    </div>

                    <div class="max-w-full overflow-x-auto custom-scrollbar px-5 pb-5">
                        <div id="monthlyChart" class="-ml-5 h-80 min-w-[690px] pl-2 xl:min-w-full"></div>
                    </div>
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
            // Monthly Statistics Chart
            @if (isset($monthlyStats) && $monthlyStats->count() > 0)
                const monthlyData = {!! $monthlyStats->toJson() !!};
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                const monthlyCategories = monthlyData.map(item => months[item.month - 1] || '');
                const monthlyTotals = monthlyData.map(item => item.total || 0);
                const monthlyCompleted = monthlyData.map(item => item.completed || 0);

                const monthlyChartOptions = {
                    series: [{
                        name: 'Total Requests',
                        data: monthlyTotals
                    }, {
                        name: 'Completed',
                        data: monthlyCompleted
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 4
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: monthlyCategories
                    },
                    yaxis: {
                        title: {
                            text: 'Number of Requests'
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + " requests"
                            }
                        }
                    },
                    colors: ['#3b82f6', '#10b981']
                };

                const monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), monthlyChartOptions);
                monthlyChart.render();
            @endif

            // Priority Distribution Chart
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
                        height: 200, // Reduced height
                        fontFamily: 'Inter, sans-serif'
                    },
                    labels: priorityLabels,
                    colors: priorityLabels.map(label => priorityColors[label] || '#6b7280'),
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%', // Make donut hole larger
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
                        enabled: false // Disable data labels on slices
                    },
                    legend: {
                        show: false // Hide legend since we have stats below
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

            // Issue Type Chart (for admin only)
            @if (auth()->user()->can('maintenance_requests.assign'))


                @if ($issueTypes > 0)
                    const issueTypeData = {!! $issueTypeStats->toJson() !!};

                    const issueTypeLabels = issueTypeData.map(item => item.name || 'Unknown');
                    const issueTypeCounts = issueTypeData.map(item => item.maintenance_requests_count || 0);

                    const issueTypeChartOptions = {
                        series: [{
                            data: issueTypeCounts
                        }],
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                horizontal: true,
                                distributed: true
                            }
                        },
                        dataLabels: {
                            enabled: true
                        },
                        xaxis: {
                            categories: issueTypeLabels
                        },
                        colors: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4']
                    };

                    const issueTypeChart = new ApexCharts(document.querySelector("#issueTypeChart"),
                        issueTypeChartOptions);
                    issueTypeChart.render();
                @else
                    document.getElementById('issueTypeChart').innerHTML =
                        '<div class="flex items-center justify-center h-64 text-gray-500">No issue type data available</div>';
                @endif
                // ===== Issue Type Line Chart =====
                @if (isset($issueTypeAnalysis) && $issueTypeAnalysis->count() > 0)
                    const issueTypeDataLine = {!! $issueTypeAnalysis->toJson() !!};
                    const issueTypeLabelsLine = issueTypeDataLine.map(item => item.name || 'Unknown');
                    const issueTypeCountsLine = issueTypeDataLine.map(item => item.count || 0);

                    new ApexCharts(document.querySelector("#issueTypeChart"), {
                        series: [{
                            name: 'Requests',
                            data: issueTypeCountsLine
                        }],
                        chart: {
                            type: 'line',
                            height: 300,
                            toolbar: {
                                show: false
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        markers: {
                            size: 5
                        },
                        xaxis: {
                            categories: issueTypeLabelsLine
                        },
                        tooltip: {
                            y: {
                                formatter: val => val + " requests"
                            }
                        },
                        colors: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444']
                    }).render();
                @endif

                // ===== Item Line Chart =====
                @if (isset($itemAnalysis) && $itemAnalysis->count() > 0)
                    const itemDataLine = {!! $itemAnalysis->toJson() !!};
                    const itemLabelsLine = itemDataLine.map(item => item.name || 'Unknown');
                    const itemCountsLine = itemDataLine.map(item => item.count || 0);

                    new ApexCharts(document.querySelector("#itemChart"), {
                        series: [{
                            name: 'Requests',
                            data: itemCountsLine
                        }],
                        chart: {
                            type: 'line',
                            height: 300,
                            toolbar: {
                                show: false
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        markers: {
                            size: 5
                        },
                        xaxis: {
                            categories: itemLabelsLine
                        },
                        tooltip: {
                            y: {
                                formatter: val => val + " requests"
                            }
                        },
                        colors: ['#f97316', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444']
                    }).render();
                @endif
            @endif

        });
    </script>
@endpush
