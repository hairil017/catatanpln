@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
<div class="p-3 sm:p-4 lg:p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen" x-data="adminDashboard()">
    <!-- Header -->
    <div class="mb-6 sm:mb-8 lg:mb-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                    <i data-lucide="layout-dashboard" class="w-8 h-8 text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                        Dashboard Admin
                    </h1>
                    <p class="text-gray-600 mt-2 text-sm sm:text-base">Monitor seluruh aktivitas dan performa sistem PLN Galesong</p>
                </div>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-3 sm:space-x-4">
                <div class="flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm rounded-lg shadow-sm border border-gray-200">
                    <i data-lucide="clock" class="w-4 h-4 text-gray-600 mr-2"></i>
                    <span class="text-sm font-medium text-gray-700" x-text="currentTime"></span>
                </div>
                <button @click="refreshData()" 
                        :disabled="loading"
                        class="flex items-center bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 sm:px-6 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base font-semibold">
                    <i data-lucide="refresh-cw" :class="loading ? 'animate-spin' : ''" class="w-4 h-4 mr-2"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- System Health Alert -->
    <div x-show="systemHealth.status === 'warning'" 
         x-transition
         class="mb-6 bg-gradient-to-r from-yellow-400 to-amber-500 rounded-xl shadow-lg p-4 border border-yellow-300">
        <div class="flex items-center">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm mr-3">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">System Warning</h3>
                <p class="text-sm text-yellow-50">Ada <span class="font-bold" x-text="systemHealth.pending_rate"></span>% laporan yang masih pending</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Total Kelompok -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-4 sm:p-5 hover:shadow-xl transition-all duration-200 border border-blue-200">
            <div class="flex flex-col items-center text-center">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md mb-3">
                    <i data-lucide="users" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Total Kelompok</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1" x-text="stats.total_kelompok"></p>
                <p class="text-xs text-gray-500">Kelompok aktif</p>
            </div>
        </div>

        <!-- Total Karyawan -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl shadow-lg p-4 sm:p-5 hover:shadow-xl transition-all duration-200 border border-green-200">
            <div class="flex flex-col items-center text-center">
                <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-md mb-3">
                    <i data-lucide="user-check" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Total Karyawan</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1" x-text="stats.total_karyawan"></p>
                <p class="text-xs text-gray-500">Karyawan terdaftar</p>
            </div>
        </div>

        <!-- Laporan Hari Ini -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-4 sm:p-5 hover:shadow-xl transition-all duration-200 border border-purple-200">
            <div class="flex flex-col items-center text-center">
                <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-md mb-3">
                    <i data-lucide="file-text" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Laporan Hari Ini</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1" x-text="stats.laporan_hari_ini"></p>
                <p class="text-xs text-gray-500">Laporan masuk hari ini</p>
            </div>
        </div>

        <!-- Total Laporan -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl shadow-lg p-4 sm:p-5 hover:shadow-xl transition-all duration-200 border border-indigo-200">
            <div class="flex flex-col items-center text-center">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-md mb-3">
                    <i data-lucide="file-text" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Total Laporan</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1" x-text="stats.total_laporan"></p>
                <p class="text-xs text-gray-500">Semua laporan karyawan</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Monthly Reports Trend -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg">
                        <i data-lucide="trending-up" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Trend Laporan Bulanan</h3>
                        <p class="text-xs text-gray-500">Grafik perkembangan laporan</p>
                    </div>
                </div>
            </div>
            <div class="chart-container relative" style="height: 300px; min-height: 300px;">
                <canvas id="monthlyReportsChart"></canvas>
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-sm">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-2"></div>
                        <p class="text-sm text-gray-600">Memuat chart...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Performance -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Performa Kelompok</h3>
                        <p class="text-xs text-gray-500">Perbandingan performa kelompok</p>
                    </div>
                </div>
            </div>
            <div class="chart-container relative" style="height: 300px; min-height: 300px;">
                <canvas id="groupPerformanceChart"></canvas>
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-sm">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mb-2"></div>
                        <p class="text-sm text-gray-600">Memuat chart...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminDashboard', () => ({
        stats: @json($stats ?? []),
        chartData: @json($chartData ?? []),
        systemHealth: {},
        currentTime: '',
        loading: false,
        charts: {},
        
        async init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
            
            // Wait for DOM to be fully ready
            await new Promise(resolve => setTimeout(resolve, 200));
            
            await this.loadStats();
            await this.loadChartData();
            
            // Auto refresh every 5 minutes
            setInterval(() => {
                this.refreshData();
            }, 300000);
        },
        
        async loadStats() {
            // Data sudah di-load dari controller, tidak perlu fetch lagi
            console.log('Stats loaded from controller:', this.stats);
            this.systemHealth = this.stats.system_health || { status: 'excellent', completion_rate: 100 };
        },
        
        async loadChartData() {
            try {
                console.log('Chart data loaded from controller:', this.chartData);
                
                // Destroy existing charts before creating new ones
                if (this.charts.monthly) {
                    this.charts.monthly.destroy();
                    this.charts.monthly = null;
                }
                if (this.charts.group) {
                    this.charts.group.destroy();
                    this.charts.group = null;
                }
                
                // Wait a bit for DOM to be ready
                await new Promise(resolve => setTimeout(resolve, 100));
                
                this.initializeCharts(this.chartData);
            } catch (error) {
                console.error('Error loading chart data:', error);
            }
        },
        
        initializeCharts(data) {
            if (!data) {
                console.error('Chart data is not available');
                return;
            }
            
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        }
                    }
                }
            };
            
            // Monthly Reports Chart
            const monthlyCtx = document.getElementById('monthlyReportsChart');
            if (monthlyCtx && data.monthly_reports) {
                try {
                    this.charts.monthly = new Chart(monthlyCtx, {
                        type: 'line',
                        data: data.monthly_reports,
                        options: {
                            ...chartOptions,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            elements: {
                                point: {
                                    radius: 4,
                                    hoverRadius: 6
                                },
                                line: {
                                    borderWidth: 2
                                }
                            }
                        }
                    });
                    console.log('Monthly reports chart initialized successfully');
                } catch (error) {
                    console.error('Error initializing monthly reports chart:', error);
                }
            } else {
                console.warn('Monthly reports chart container or data not found');
            }
            
            // Group Performance Chart
            const groupCtx = document.getElementById('groupPerformanceChart');
            if (groupCtx && data.group_performance) {
                try {
                    this.charts.group = new Chart(groupCtx, {
                        type: 'bar',
                        data: data.group_performance,
                        options: {
                            ...chartOptions,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            elements: {
                                bar: {
                                    borderRadius: 4,
                                    borderSkipped: false
                                }
                            }
                        }
                    });
                    console.log('Group performance chart initialized successfully');
                } catch (error) {
                    console.error('Error initializing group performance chart:', error);
                }
            } else {
                console.warn('Group performance chart container or data not found');
            }
            
            // Handle window resize for charts
            window.addEventListener('resize', () => {
                Object.values(this.charts).forEach(chart => {
                    if (chart) {
                        chart.resize();
                    }
                });
            });
        },
        
        async refreshData() {
            this.loading = true;
            try {
                // Reload page to get fresh data from server
                window.location.reload();
            } catch (error) {
                console.error('Error refreshing data:', error);
                this.loading = false;
            }
        },
        
        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }));
});
</script>
@endsection
