@extends('layouts.app')

@section('content')
    <div class="space-y-4">
        <!-- System Metrics - FIXED: Removed overflow-x-auto -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6 sm:overflow-visible overflow-x-auto">
            @php
                $stats = [
                    [
                        'label' => 'Total Users',
                        'value' => number_format($totalUsers),
                        'icon' =>
                            'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z',
                        'color' => 'blue',
                        'badge' => ($totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0) . '%',
                    ],
                    [
                        'label' => 'Active Users',
                        'value' => number_format($activeUsers),
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' => 'green',
                        'badge' => null,
                    ],
                    [
                        'label' => 'Total Roles',
                        'value' => number_format($totalRoles),
                        'icon' => 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5',
                        'color' => 'purple',
                        'badge' => null,
                    ],
                    [
                        'label' => 'Structures',
                        'value' => $totalDivisions . 'D / ' . $totalClusters . 'C',
                        'icon' =>
                            'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'color' => 'indigo',
                        'badge' => null,
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div
                    class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-2xl dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-xl 
                    {{ $stat['color'] == 'blue' ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}
                    {{ $stat['color'] == 'green' ? 'bg-green-50 dark:bg-green-500/10' : '' }}
                    {{ $stat['color'] == 'purple' ? 'bg-purple-50 dark:bg-purple-500/10' : '' }}
                    {{ $stat['color'] == 'indigo' ? 'bg-indigo-50 dark:bg-indigo-500/10' : '' }}
                ">
                            <svg class="w-5 h-5 
                        {{ $stat['color'] == 'blue' ? 'fill-blue-600 dark:fill-blue-400' : '' }}
                        {{ $stat['color'] == 'green' ? 'fill-green-600 dark:fill-green-400' : '' }}
                        {{ $stat['color'] == 'purple' ? 'fill-purple-600 dark:fill-purple-400' : '' }}
                        {{ $stat['color'] == 'indigo' ? 'fill-indigo-600 dark:fill-indigo-400' : '' }}
                    "
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="{{ $stat['icon'] }}" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-none mb-1">
                                {{ $stat['label'] }}
                            </p>
                            <h4 class="text-lg font-bold text-gray-800 dark:text-white/90 leading-none">
                                {{ $stat['value'] }}
                            </h4>
                        </div>
                    </div>

                    @if ($stat['badge'])
                        <span
                            class="px-2 py-0.5 text-xs font-bold rounded-full bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                            {{ $stat['badge'] }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <!-- User Growth Chart -->
            <div class="col-span-12 lg:col-span-8">
                <div
                    class="h-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between px-5 pt-5 sm:px-6 sm:pt-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">User Growth</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monthly user registration</p>
                        </div>
                        <x-common.dropdown-menu :items="['This year', 'Last year', 'All time']" />
                    </div>
                    <div class="px-5 pb-5">
                        <div id="userGrowthChart" class="h-72"></div>
                    </div>
                </div>
            </div>

            <!-- Role Distribution -->
            <div class="col-span-12 lg:col-span-4">
                <div
                    class="h-full overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between px-5 pt-5 sm:px-6 sm:pt-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Role Distribution</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Users by role</p>
                        </div>
                        <x-common.dropdown-menu :items="['View Details', 'Export']" />
                    </div>
                    <div class="px-5 pb-5">
                        <div id="roleDistributionChart" class="h-64"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Stats & Recent Users -->
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <!-- Activity Statistics -->
            {{-- <div class="col-span-12 lg:col-span-4">
                <div
                    class="h-full rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 mb-6">User Activity</h3>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50 dark:bg-blue-500/5">
                            <div>
                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">With Activity</span>
                                <p class="text-xs text-blue-500 dark:text-blue-400/80">Logged in at least once</p>
                            </div>
                            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $usersWithActivity }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-green-50 dark:bg-green-500/5">
                            <div>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">Last 7 Days</span>
                                <p class="text-xs text-green-500 dark:text-green-400/80">Active users</p>
                            </div>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                {{ $usersLastWeek }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-purple-50 dark:bg-purple-500/5">
                            <div>
                                <span class="text-sm font-medium text-purple-600 dark:text-purple-400">Last 30 Days</span>
                                <p class="text-xs text-purple-500 dark:text-purple-400/80">Active users</p>
                            </div>
                            <span class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $usersLastMonth }}
                            </span>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Inactive Users</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Never logged in</p>
                                </div>
                                <span class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                    {{ $inactiveUsers }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Recent Users -->
            <div class="col-span-12 lg:col-span-8">
                <div
                    class="h-full rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between px-5 pt-5 sm:px-6 sm:pt-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Recent Users</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest registered users</p>
                        </div>
                        <a href="{{ route('users.index') }}"
                            class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                            View All →
                        </a>
                    </div>

                    <div class="px-5 pb-5">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <th class="pb-3 text-left">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">USER</p>
                                        </th>
                                        <th class="pb-3 text-left">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">ROLE</p>
                                        </th>
                                        <th class="pb-3 text-left">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">DIVISION</p>
                                        </th>
                                        <th class="pb-3 text-left">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">STATUS</p>
                                        </th>
                                        <th class="pb-3 text-left">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">JOINED</p>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentUsers as $recentUser)
                                        <tr class="border-b border-gray-100 last:border-b-0 dark:border-gray-800">
                                            <td class="py-3 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <img class="h-8 w-8 rounded-full" src="{{ $recentUser->avatar_url }}"
                                                        alt="{{ $recentUser->full_name }}">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                            {{ $recentUser->full_name }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $recentUser->email }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                                    {{ $recentUser->roles->first()->name ?? 'No Role' }}
                                                </span>
                                            </td>
                                            <td class="py-3 whitespace-nowrap">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $recentUser->division?->name ?? 'N/A' }}
                                                </p>
                                            </td>
                                            <td class="py-3 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center rounded-full {{ $recentUser->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }} px-2.5 py-0.5 text-xs font-medium">
                                                    {{ $recentUser->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="py-3 whitespace-nowrap">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $recentUser->created_at->format('M d, Y') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Division Distribution -->
        <div class="col-span-12">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between px-5 pt-5 sm:px-6 sm:pt-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Division Distribution</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Users by division</p>
                    </div>
                    <x-common.dropdown-menu :items="['Top 10', 'All Divisions', 'Export']" />
                </div>
                <div class="px-5 pb-5">
                    <div id="divisionChart" class="h-64"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Growth Chart
            @if (isset($monthlyUserStats) && $monthlyUserStats->count() > 0)
                const monthlyUserData = {!! $monthlyUserStats->toJson() !!};
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                const monthlyUserCategories = monthlyUserData.map(item => months[item.month - 1] || '');
                const monthlyUserTotals = monthlyUserData.map(item => item.total_users || 0);
                const monthlyActiveUsers = monthlyUserData.map(item => item.active_users || 0);

                const userGrowthChartOptions = {
                    series: [{
                        name: 'Total Users',
                        data: monthlyUserTotals
                    }, {
                        name: 'Active Users',
                        data: monthlyActiveUsers
                    }],
                    chart: {
                        type: 'area',
                        height: '100%',
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: ['#3b82f6', '#10b981'],
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    xaxis: {
                        categories: monthlyUserCategories,
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#6b7280',
                                fontSize: '12px'
                            }
                        }
                    },
                    tooltip: {
                        style: {
                            fontSize: '12px'
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '12px',
                        markers: {
                            radius: 4
                        }
                    }
                };

                const userGrowthChart = new ApexCharts(document.querySelector("#userGrowthChart"),
                    userGrowthChartOptions);
                userGrowthChart.render();
            @endif

            // Role Distribution Chart
            @if (isset($roleDistribution) && $roleDistribution->count() > 0)
                const roleData = {!! $roleDistribution->toJson() !!};
                const roleLabels = roleData.map(item => item.name || 'Unknown');
                const roleCounts = roleData.map(item => item.users_count || 0);

                const roleChartOptions = {
                    series: roleCounts,
                    chart: {
                        type: 'donut',
                        height: '100%',
                        fontFamily: 'Inter, sans-serif'
                    },
                    labels: roleLabels,
                    colors: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4'],
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
                                        label: 'Total Users',
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
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '12px'
                    }
                };

                const roleDistributionChart = new ApexCharts(document.querySelector("#roleDistributionChart"),
                    roleChartOptions);
                roleDistributionChart.render();
            @endif


            // Division Distribution Chart (COUNT + DECIMAL PERCENT)


            @if (isset($divisionDistribution) && $divisionDistribution->count() > 0)
                const divisionData = {!! $divisionDistribution->toJson() !!};

                const divisionLabels = divisionData.map(item => item.name || 'Unknown');
                const divisionCounts = divisionData.map(item => item.users_count || 0);

                const totalUsers = divisionCounts.reduce((sum, val) => sum + val, 0);

                const divisionPercentages = divisionCounts.map(count =>
                    totalUsers > 0 ? Number(((count / totalUsers) * 100).toFixed(1)) : 0
                );

                const divisionChartOptions = {
                    series: [{
                        name: 'Users',
                        data: divisionCounts
                    }],
                    chart: {
                        type: 'line',
                        height: '100%',
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 6,
                        hover: {
                            size: 8
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            const index = opts.dataPointIndex;
                            return `${val} (${divisionPercentages[index]}%)`;
                        },
                        style: {
                            fontSize: '11px'
                        }
                    },
                    xaxis: {
                        categories: divisionLabels,
                        labels: {
                            rotate: -30,
                            style: {
                                fontSize: '12px',
                                colors: '#6b7280'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Number of Users'
                        },
                        labels: {
                            formatter: function(val) {
                                return Math.round(val);
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val, opts) {
                                const index = opts.dataPointIndex;
                                return `${val} users (${divisionPercentages[index]}%)`;
                            }
                        }
                    },
                    colors: ['#3b82f6']
                };

                const divisionChart = new ApexCharts(
                    document.querySelector("#divisionChart"),
                    divisionChartOptions
                );
                divisionChart.render();
            @endif



        });
    </script>
@endpush
