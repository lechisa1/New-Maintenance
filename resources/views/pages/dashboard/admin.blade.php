@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- System Metrics - Professional Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $stats = [
                    [
                        'label' => 'Total Users',
                        'value' => number_format($totalUsers),
                        'icon' =>
                            'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z',
                        'color' => 'blue',
                        'trend' => '+12%',
                        'trendUp' => true,
                    ],
                    [
                        'label' => 'Active Users',
                        'value' => number_format($activeUsers),
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' => 'green',
                        'trend' => '+5%',
                        'trendUp' => true,
                    ],
                    [
                        'label' => 'Total Roles',
                        'value' => number_format($totalRoles),
                        'icon' => 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5',
                        'color' => 'purple',
                        'trend' => 'Stable',
                        'trendUp' => null,
                    ],
                    [
                        'label' => 'Structures',
                        'value' => $totalDivisions . 'D / ' . $totalClusters . 'C',
                        'icon' =>
                            'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'color' => 'indigo',
                        'trend' => '8 Divisions',
                        'trendUp' => null,
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div
                    class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-white to-gray-50 p-5 shadow-lg transition-all duration-300 hover:shadow-xl dark:from-gray-800 dark:to-gray-900/50">
                    <div
                        class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-gradient-to-br opacity-10 blur-2xl transition-all duration-300 group-hover:scale-150
                        {{ $stat['color'] == 'blue' ? 'from-blue-400 to-blue-600' : '' }}
                        {{ $stat['color'] == 'green' ? 'from-green-400 to-green-600' : '' }}
                        {{ $stat['color'] == 'purple' ? 'from-purple-400 to-purple-600' : '' }}
                        {{ $stat['color'] == 'indigo' ? 'from-indigo-400 to-indigo-600' : '' }}">
                    </div>

                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br shadow-lg
                                    {{ $stat['color'] == 'blue' ? 'from-blue-500 to-blue-600' : '' }}
                                    {{ $stat['color'] == 'green' ? 'from-green-500 to-green-600' : '' }}
                                    {{ $stat['color'] == 'purple' ? 'from-purple-500 to-purple-600' : '' }}
                                    {{ $stat['color'] == 'indigo' ? 'from-indigo-500 to-indigo-600' : '' }}">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $stat['icon'] }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                                    <h4 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        {{ $stat['value'] }}</h4>
                                </div>
                            </div>
                            @if ($stat['trend'])
                                <div
                                    class="flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold
                                    {{ $stat['trendUp'] === true ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $stat['trendUp'] === false ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                    {{ $stat['trendUp'] === null ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                    @if ($stat['trendUp'] === true)
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    @elseif($stat['trendUp'] === false)
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                                        </svg>
                                    @endif
                                    <span>{{ $stat['trend'] }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Mini sparkline -->
                        <div class="mt-4 h-12">
                            <div class="sparkline" data-color="{{ $stat['color'] }}"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Charts Section -->
        <!-- Charts Section -->
        <div class="grid grid-cols-12 gap-6">
            <!-- User Growth Chart -->
            <div class="col-span-12 lg:col-span-7">
                <div class="rounded-2xl bg-white shadow-lg transition-all duration-300 hover:shadow-xl dark:bg-gray-800/50">
                    <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">User Growth Analytics</h3>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Monthly user registration &
                                    activity trends</p>
                            </div>
                            <div class="flex gap-1.5">
                                <button onclick="changeChartTimeframe('year')"
                                    class="timeframe-btn active rounded-lg px-2.5 py-1 text-xs font-medium transition-all duration-200 bg-blue-600 text-white shadow-sm hover:bg-blue-700">Year</button>
                                <button onclick="changeChartTimeframe('quarter')"
                                    class="timeframe-btn rounded-lg px-2.5 py-1 text-xs font-medium transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">Quarter</button>
                                <button onclick="changeChartTimeframe('month')"
                                    class="timeframe-btn rounded-lg px-2.5 py-1 text-xs font-medium transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">Month</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div id="userGrowthChart" class="h-80 w-full"></div>
                    </div>
                </div>
            </div>

            <!-- Role Distribution -->
            <div class="col-span-12 lg:col-span-5">
                <div class="rounded-2xl bg-white shadow-lg transition-all duration-300 hover:shadow-xl dark:bg-gray-800/50">
                    <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Role Distribution</h3>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">User breakdown by role</p>
                    </div>
                    <div class="p-4">
                        <div id="roleDistributionChart" class="h-72 w-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Overview & Recent Users -->
        <div class="grid grid-cols-12 gap-6">


            <!-- Recent Users - Modern Table -->
            <div class="col-span-12 lg:col-span-12">
                <div class="rounded-2xl bg-white shadow-lg transition-all duration-300 hover:shadow-xl dark:bg-gray-800/50">
                    <div class="flex items-center justify-between border-b border-gray-200 p-6 dark:border-gray-700">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Recent Users</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest registered users</p>
                        </div>
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-600 transition-all duration-200 hover:bg-blue-100 hover:shadow-md dark:bg-blue-900/30 dark:text-blue-400">
                            View All
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        User</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Division</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($recentUsers as $recentUser)
                                    <tr class="transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600"
                                                    src="{{ $recentUser->avatar_url }}"
                                                    alt="{{ $recentUser->full_name }}">
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">
                                                        {{ $recentUser->full_name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $recentUser->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                class="inline-flex rounded-full bg-gradient-to-r from-purple-500 to-purple-600 px-3 py-1 text-xs font-semibold text-white shadow-sm">
                                                {{ $recentUser->roles->first()->name ?? 'No Role' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $recentUser->division?->name ?? 'N/A' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold
                                                {{ $recentUser->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400' }}">
                                                <span class="relative flex h-2 w-2">
                                                    <span
                                                        class="absolute inline-flex h-full w-full animate-ping rounded-full {{ $recentUser->is_active ? 'bg-green-400' : 'bg-gray-400' }} opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex h-2 w-2 rounded-full {{ $recentUser->is_active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                                                </span>
                                                {{ $recentUser->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $recentUser->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Division Distribution - Advanced Horizontal Bar Chart -->
        <div class="col-span-12">
            <div class="rounded-2xl bg-white shadow-lg transition-all duration-300 hover:shadow-xl dark:bg-gray-800/50">
                <div class="border-b border-gray-200 p-6 dark:border-gray-700">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Division Distribution</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">User distribution across divisions</p>
                        </div>
                        @if (isset($divisionDistribution) && $divisionDistribution->count() > 0)
                            <div class="flex gap-2">
                                <button onclick="sortDivisionChart('count')"
                                    class="sort-btn active rounded-lg px-3 py-1.5 text-xs font-medium bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition-all">
                                    By Count
                                </button>
                                <button onclick="sortDivisionChart('name')"
                                    class="sort-btn rounded-lg px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 transition-all">
                                    By Name
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <div id="divisionChart" style="min-height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // User Growth Chart - Professional Area Chart with Fixed Overlapping Issues
            @if (isset($monthlyUserStats) && $monthlyUserStats->count() > 0)
                const monthlyUserData = {!! $monthlyUserStats->toJson() !!};
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                const monthlyUserCategories = monthlyUserData.map(item => months[item.month - 1] || '');
                const monthlyUserTotals = monthlyUserData.map(item => item.total_users || 0);
                const monthlyActiveUsers = monthlyUserData.map(item => item.active_users || 0);

                // Calculate growth percentages
                const growthRates = monthlyUserTotals.map((val, i) => {
                    if (i === 0) return 0;
                    return ((val - monthlyUserTotals[i - 1]) / monthlyUserTotals[i - 1] * 100).toFixed(1);
                });

                const userGrowthChartOptions = {
                    series: [{
                            name: 'Total Users',
                            data: monthlyUserTotals,
                            type: 'area'
                        },
                        {
                            name: 'Active Users',
                            data: monthlyActiveUsers,
                            type: 'area'
                        }
                    ],
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: true,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: true,
                                reset: true
                            },
                            autoSelected: 'zoom'
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        },
                        zoom: {
                            enabled: true,
                            type: 'x',
                            autoScaleYaxis: true
                        },
                        background: 'transparent',
                        fontFamily: 'Inter, system-ui, -apple-system, sans-serif'
                    },
                    colors: ['#3b82f6', '#10b981'],
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    xaxis: {
                        categories: monthlyUserCategories,
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '11px',
                                fontWeight: 500
                            },
                            rotate: -15,
                            rotateAlways: false,
                            hideOverlappingLabels: true,
                            trim: true
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        title: {
                            text: 'Month',
                            style: {
                                fontSize: '12px',
                                fontWeight: 500,
                                color: '#6b7280'
                            },
                            offsetY: 5
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '11px',
                                fontWeight: 500
                            },
                            formatter: function(val) {
                                if (val >= 1000) {
                                    return (val / 1000).toFixed(1) + 'k';
                                }
                                return Math.round(val);
                            },
                            offsetX: -5
                        },
                        title: {
                            text: 'Number of Users',
                            style: {
                                fontSize: '12px',
                                fontWeight: 500,
                                color: '#6b7280'
                            },
                            offsetX: 0,
                            offsetY: 0
                        },
                        min: 0,
                        forceNiceScale: true,
                        tickAmount: 6
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function(val, {
                                series,
                                seriesIndex,
                                dataPointIndex
                            }) {
                                const formattedVal = Math.round(val).toLocaleString();
                                if (seriesIndex === 0 && dataPointIndex > 0) {
                                    const growth = growthRates[dataPointIndex];
                                    const growthSymbol = growth > 0 ? '↑' : (growth < 0 ? '↓' : '→');
                                    const growthColor = growth > 0 ? '#10b981' : (growth < 0 ? '#ef4444' :
                                        '#6b7280');
                                    return `${formattedVal} users <span style="color: ${growthColor}">${growthSymbol} ${Math.abs(growth)}%</span>`;
                                }
                                return `${formattedVal} users`;
                            }
                        },
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Inter, system-ui, sans-serif'
                        },
                        theme: 'dark'
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'center',
                        fontSize: '12px',
                        fontWeight: 500,
                        markers: {
                            radius: 4,
                            width: 10,
                            height: 10
                        },
                        itemMargin: {
                            horizontal: 15,
                            vertical: 5
                        },
                        offsetY: -5,
                        onItemClick: {
                            toggleDataSeries: true
                        },
                        onItemHover: {
                            highlightDataSeries: true
                        }
                    },
                    grid: {
                        borderColor: '#e5e7eb',
                        strokeDashArray: 5,
                        position: 'back',
                        xaxis: {
                            lines: {
                                show: false
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        },
                        padding: {
                            left: 5,
                            right: 5
                        }
                    },
                    markers: {
                        size: 4,
                        colors: ['#fff'],
                        strokeColors: ['#3b82f6', '#10b981'],
                        strokeWidth: 2,
                        hover: {
                            size: 6,
                            sizeOffset: 2
                        },
                        discrete: []
                    },
                    responsive: [{
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 300
                                },
                                yaxis: {
                                    labels: {
                                        fontSize: '10px'
                                    }
                                },
                                xaxis: {
                                    labels: {
                                        rotate: -30,
                                        fontSize: '10px'
                                    }
                                },
                                legend: {
                                    position: 'bottom',
                                    fontSize: '11px'
                                }
                            }
                        },
                        {
                            breakpoint: 480,
                            options: {
                                chart: {
                                    height: 280
                                },
                                xaxis: {
                                    labels: {
                                        rotate: -45,
                                        fontSize: '9px'
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        fontSize: '9px'
                                    }
                                }
                            }
                        }
                    ],
                    subtitle: {
                        text: 'Click and drag to zoom • Double click to reset',
                        align: 'center',
                        style: {
                            fontSize: '10px',
                            color: '#9ca3af'
                        },
                        offsetY: 335
                    }
                };

                const userGrowthChart = new ApexCharts(document.querySelector("#userGrowthChart"),
                    userGrowthChartOptions);
                userGrowthChart.render();

                // Add custom CSS to fix any remaining overlap issues
                const style = document.createElement('style');
                style.textContent = `
        #userGrowthChart {
            width: 100%;
            overflow-x: visible;
        }
        .apexcharts-legend {
            gap: 15px !important;
        }
        .apexcharts-legend-series {
            margin: 0 10px !important;
        }
        .apexcharts-tooltip {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(8px) !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            border: 1px solid #e5e7eb !important;
        }
        .apexcharts-tooltip-text-y-label {
            font-weight: 600 !important;
        }
        @media (max-width: 768px) {
            .apexcharts-legend-text {
                font-size: 10px !important;
            }
            .apexcharts-yaxis-title text {
                font-size: 10px !important;
            }
        }
    `;
                document.head.appendChild(style);

                // Timeframe switching function
                window.changeChartTimeframe = function(timeframe) {
                    const buttons = document.querySelectorAll('.timeframe-btn');
                    buttons.forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('bg-gray-100', 'text-gray-700');
                    });
                    event.target.classList.add('bg-blue-600', 'text-white');
                    event.target.classList.remove('bg-gray-100', 'text-gray-700');

                    // Add loading state
                    const chartContainer = document.querySelector("#userGrowthChart");
                    chartContainer.style.opacity = '0.5';

                    // Simulate data update (replace with actual API call)
                    setTimeout(() => {
                        chartContainer.style.opacity = '1';
                        // Update chart data here based on timeframe
                    }, 500);
                };
            @endif

            // Role Distribution Chart - Fixed Overlapping Issues
            @if (isset($roleDistribution) && $roleDistribution->count() > 0)
                const roleData = {!! $roleDistribution->toJson() !!};
                const roleLabels = roleData.map(item => item.name || 'Unknown');
                const roleCounts = roleData.map(item => item.users_count || 0);
                const totalRolesCount = roleCounts.reduce((a, b) => a + b, 0);

                const roleChartOptions = {
                    series: roleCounts,
                    chart: {
                        type: 'donut',
                        height: 300,
                        fontFamily: 'Inter, system-ui, sans-serif',
                        toolbar: {
                            show: true,
                            tools: {
                                download: true
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    labels: roleLabels,
                    colors: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899',
                        '#6366f1'
                    ],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '13px',
                                        fontWeight: 600,
                                        color: '#1f2937',
                                        offsetY: -8
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '20px',
                                        fontWeight: 'bold',
                                        color: '#1f2937',
                                        formatter: function(val) {
                                            return val.toLocaleString();
                                        },
                                        offsetY: 8
                                    },
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        label: 'Total',
                                        fontSize: '12px',
                                        fontWeight: 500,
                                        color: '#6b7280',
                                        formatter: function(w) {
                                            return totalRolesCount.toLocaleString();
                                        }
                                    }
                                }
                            },
                            expandOnClick: true,
                            customScale: 1
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '10px',
                            fontWeight: 500,
                            colors: ['#fff']
                        },
                        formatter: function(val, opts) {
                            const percent = ((val / totalRolesCount) * 100).toFixed(1);
                            return `${percent}%`;
                        },
                        dropShadow: {
                            enabled: false
                        },
                        background: {
                            enabled: false
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val, {
                                series,
                                seriesIndex,
                                dataPointIndex,
                                w
                            }) {
                                const percent = ((val / totalRolesCount) * 100).toFixed(1);
                                return `${val.toLocaleString()} users (${percent}%)`;
                            }
                        },
                        style: {
                            fontSize: '12px'
                        }
                    },
                    legend: {
                        show: true,
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '11px',
                        fontWeight: 500,
                        markers: {
                            width: 8,
                            height: 8,
                            radius: 4
                        },
                        itemMargin: {
                            horizontal: 8,
                            vertical: 4
                        },
                        formatter: function(seriesName, opts) {
                            const count = roleCounts[opts.seriesIndex];
                            const percent = ((count / totalRolesCount) * 100).toFixed(1);
                            // Truncate long role names
                            const shortName = seriesName.length > 15 ? seriesName.substring(0, 12) + '...' :
                                seriesName;
                            return `${shortName}: ${count} (${percent}%)`;
                        },
                        offsetY: 10
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.1
                            }
                        }
                    },
                    responsive: [{
                            breakpoint: 1024,
                            options: {
                                chart: {
                                    height: 280
                                },
                                legend: {
                                    fontSize: '10px',
                                    itemMargin: {
                                        horizontal: 6,
                                        vertical: 3
                                    }
                                }
                            }
                        },
                        {
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 260
                                },
                                legend: {
                                    position: 'bottom',
                                    fontSize: '9px',
                                    itemMargin: {
                                        horizontal: 5,
                                        vertical: 2
                                    }
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '60%',
                                            labels: {
                                                name: {
                                                    fontSize: '11px'
                                                },
                                                value: {
                                                    fontSize: '16px'
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        {
                            breakpoint: 480,
                            options: {
                                chart: {
                                    height: 240
                                },
                                legend: {
                                    show: true,
                                    position: 'bottom',
                                    fontSize: '8px',
                                    itemMargin: {
                                        horizontal: 4,
                                        vertical: 2
                                    }
                                },
                                dataLabels: {
                                    style: {
                                        fontSize: '8px'
                                    }
                                }
                            }
                        }
                    ]
                };

                const roleDistributionChart = new ApexCharts(document.querySelector("#roleDistributionChart"),
                    roleChartOptions);
                roleDistributionChart.render();
            @endif


            // Division Distribution Chart - Fixed Horizontal Bar Chart
            @if (isset($divisionDistribution) && $divisionDistribution->count() > 0)
                let divisionData = {!! $divisionDistribution->toJson() !!};
                let currentSort = 'count';

                function updateDivisionChart(sortBy) {
                    let sortedData = [...divisionData];
                    if (sortBy === 'count') {
                        sortedData.sort((a, b) => (b.users_count || 0) - (a.users_count || 0));
                    } else {
                        sortedData.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
                    }

                    // Filter out divisions with 0 users
                    sortedData = sortedData.filter(item => (item.users_count || 0) > 0);

                    if (sortedData.length === 0) {
                        document.querySelector("#divisionChart").innerHTML =
                            '<div class="flex items-center justify-center h-64 text-gray-500">No division data available</div>';
                        return;
                    }

                    const divisionLabels = sortedData.map(item => {
                        let name = item.name || 'Unknown';
                        // Truncate long names
                        return name.length > 30 ? name.substring(0, 27) + '...' : name;
                    });
                    const divisionCounts = sortedData.map(item => item.users_count || 0);
                    const totalDivisionUsers = divisionCounts.reduce((a, b) => a + b, 0);
                    const divisionPercentages = divisionCounts.map(count =>
                        totalDivisionUsers > 0 ? ((count / totalDivisionUsers) * 100).toFixed(1) : 0
                    );

                    const maxCount = Math.max(...divisionCounts);
                    const barColors = divisionCounts.map(count => {
                        const intensity = count / maxCount;
                        // Generate gradient colors based on count
                        if (intensity > 0.7) return '#3b82f6';
                        if (intensity > 0.4) return '#60a5fa';
                        return '#93c5fd';
                    });

                    const divisionChartOptions = {
                        series: [{
                            name: 'Number of Users',
                            data: divisionCounts
                        }],
                        chart: {
                            type: 'bar', // Changed from 'line' to 'bar'
                            height: Math.min(500, Math.max(350, divisionLabels.length * 35)),
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    zoom: true,
                                    pan: true,
                                    reset: true
                                }
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800
                            },
                            fontFamily: 'Inter, system-ui, sans-serif'
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 6,
                                horizontal: true,
                                barHeight: '70%',
                                distributed: true,
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val, opt) {
                                const percent = divisionPercentages[opt.dataPointIndex];
                                return `${val} (${percent}%)`;
                            },
                            offsetX: 10,
                            style: {
                                fontSize: '11px',
                                fontWeight: 500,
                                colors: ['#1f2937']
                            },
                            background: {
                                enabled: false
                            }
                        },
                        xaxis: {
                            categories: divisionLabels,
                            title: {
                                text: 'Number of Users',
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 500,
                                    color: '#6b7280'
                                }
                            },
                            labels: {
                                formatter: function(val) {
                                    return Math.round(val);
                                },
                                style: {
                                    fontSize: '11px',
                                    colors: '#6b7280'
                                }
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 500,
                                    colors: '#374151'
                                },
                                formatter: function(val) {
                                    // Truncate long labels
                                    return val.length > 30 ? val.substring(0, 27) + '...' : val;
                                }
                            },
                            title: {
                                text: 'Division',
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 500,
                                    color: '#6b7280'
                                }
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(val, {
                                    dataPointIndex
                                }) {
                                    const percent = divisionPercentages[dataPointIndex];
                                    return `${val} users (${percent}% of total)`;
                                }
                            },
                            style: {
                                fontSize: '12px'
                            },
                            theme: 'dark'
                        },
                        colors: barColors,
                        grid: {
                            borderColor: '#e5e7eb',
                            strokeDashArray: 5,
                            xaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            padding: {
                                left: 10,
                                right: 10
                            }
                        },
                        title: {
                            text: `Division Distribution (${totalDivisionUsers} Total Users)`,
                            align: 'left',
                            style: {
                                fontSize: '13px',
                                fontWeight: 500,
                                color: '#6b7280'
                            },
                            offsetX: 0,
                            offsetY: 0
                        },
                        legend: {
                            show: false
                        },
                        states: {
                            hover: {
                                filter: {
                                    type: 'darken',
                                    value: 0.1
                                }
                            }
                        },
                        responsive: [{
                                breakpoint: 1024,
                                options: {
                                    chart: {
                                        height: Math.min(450, Math.max(300, divisionLabels.length * 30))
                                    },
                                    dataLabels: {
                                        style: {
                                            fontSize: '10px'
                                        }
                                    }
                                }
                            },
                            {
                                breakpoint: 768,
                                options: {
                                    chart: {
                                        height: Math.min(400, Math.max(280, divisionLabels.length * 28))
                                    },
                                    plotOptions: {
                                        bar: {
                                            barHeight: '60%'
                                        }
                                    },
                                    dataLabels: {
                                        style: {
                                            fontSize: '9px'
                                        },
                                        offsetX: 5
                                    },
                                    xaxis: {
                                        labels: {
                                            fontSize: '10px'
                                        }
                                    },
                                    yaxis: {
                                        labels: {
                                            fontSize: '10px'
                                        }
                                    }
                                }
                            },
                            {
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        height: Math.min(350, Math.max(250, divisionLabels.length * 25))
                                    },
                                    plotOptions: {
                                        bar: {
                                            barHeight: '50%'
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false // Disable data labels on very small screens
                                    },
                                    xaxis: {
                                        labels: {
                                            fontSize: '9px'
                                        }
                                    },
                                    yaxis: {
                                        labels: {
                                            fontSize: '9px'
                                        }
                                    }
                                }
                            }
                        ]
                    };

                    // Destroy existing chart if it exists
                    if (window.divisionChart && typeof window.divisionChart.destroy === 'function') {
                        window.divisionChart.destroy();
                    }

                    // Create new chart
                    const chartElement = document.querySelector("#divisionChart");
                    if (chartElement) {
                        window.divisionChart = new ApexCharts(chartElement, divisionChartOptions);
                        window.divisionChart.render();
                    }
                }

                // Initialize the chart
                if (divisionData.length > 0) {
                    updateDivisionChart('count');
                } else {
                    document.querySelector("#divisionChart").innerHTML =
                        '<div class="flex items-center justify-center h-64 text-gray-500">No division data available</div>';
                }

                // Sort function
                window.sortDivisionChart = function(sortBy) {
                    currentSort = sortBy;
                    const buttons = document.querySelectorAll('.sort-btn');
                    buttons.forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700',
                            'dark:text-gray-300');
                    });
                    if (event && event.target) {
                        event.target.classList.add('bg-blue-600', 'text-white');
                        event.target.classList.remove('bg-gray-100', 'text-gray-700', 'dark:bg-gray-700',
                            'dark:text-gray-300');
                    }
                    updateDivisionChart(sortBy);
                };
            @else
                // No data fallback
                document.addEventListener('DOMContentLoaded', function() {
                    const divisionChartElement = document.querySelector("#divisionChart");
                    if (divisionChartElement) {
                        divisionChartElement.innerHTML =
                            '<div class="flex items-center justify-center h-64 text-gray-500">No division data available</div>';
                    }
                });
            @endif
        });
    </script>
@endpush
