@extends('layouts.dashboard')

@section('title', 'Dashboard Kelompok')

@section('content')
<div class="p-6" x-data="kelompokDashboard()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Kelompok</h1>
                <p class="text-gray-600 mt-2">Selamat datang, <span x-text="stats.nama_kelompok"></span> - <span x-text="stats.shift"></span></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                    <span x-text="currentTime"></span>
                </div>
                <button @click="refreshData()" 
                        class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Performance Alert -->
    @if(isset($userRanking) && $userRanking)
    <div class="mb-6 {{ $userRanking['rank'] <= 3 ? 'bg-green-50 border border-green-200' : 'bg-blue-50 border border-blue-200' }} rounded-lg p-4">
        <div class="flex items-center">
            <i data-lucide="{{ $userRanking['rank'] <= 3 ? 'award' : 'bar-chart-2'}}" 
               class="w-5 h-5 {{ $userRanking['rank'] <= 3 ? 'text-green-600' : 'text-blue-600' }} mr-3"></i>
            <div>
                <h3 class="text-sm font-medium {{ $userRanking['rank'] <= 3 ? 'text-green-800' : 'text-blue-800' }}">Performa Terbaik!</h3>
                <p class="text-sm {{ $userRanking['rank'] <= 3 ? 'text-green-700' : 'text-blue-700' }}">Kelompok Anda berada di peringkat <span class="font-semibold">{{ $userRanking['rank'] }}</span> dari <span class="font-semibold">{{ $userRanking['total_groups'] }}</span> kelompok</p>
                <p class="text-xs {{ $userRanking['rank'] <= 3 ? 'text-green-600' : 'text-blue-600' }} mt-1">Skor Performa: <span class="font-semibold">{{ number_format($userRanking['skor'], 2) }}</span>/100</p>
                <p class="text-xs {{ $userRanking['rank'] <= 3 ? 'text-green-500' : 'text-blue-500' }} mt-1 italic">Berdasarkan data dari Grafik Peringkat Performa Kelompok</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Ranking Chart -->
    @if(isset($performanceData) && count($performanceData) > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Grafik Peringkat Performa Kelompok</h3>
        <div class="relative" id="performanceRankingChart" style="min-height: 400px;"></div>
        <div class="mt-4">
            <p class="text-xs text-gray-500 leading-relaxed">
                <strong>Keterangan:</strong> Peringkat dihitung berdasarkan kombinasi Jumlah Laporan (40%), Rata-rata Waktu Penyelesaian (30%), dan Konsistensi (30%). 
                Grafik ini menggunakan data gabungan dari <strong>Laporan Karyawan</strong> dan <strong>Job Pekerjaan</strong>, dimana 
                <strong>Laporan Karyawan</strong> digunakan untuk menghitung jumlah laporan bulan ini (40%) dan konsistensi harian/mingguan (30%), 
                sedangkan <strong>Job Pekerjaan</strong> digunakan untuk menghitung rata-rata waktu penyelesaian (30%).
            </p>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Anggota -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Anggota</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.total_anggota"></p>
                </div>
            </div>
        </div>

        <!-- Laporan Hari Ini -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="file-text" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.laporan_hari_ini"></p>
                </div>
            </div>
        </div>

        <!-- Laporan Bulan Ini -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i data-lucide="calendar" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.laporan_bulan_ini"></p>
                </div>
            </div>
        </div>

        <!-- Rata-rata Waktu -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata Waktu</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="(stats.avg_waktu_penyelesaian || 0).toFixed(1) + ' hari'"></p>
                </div>
            </div>
        </div>
    </div>

</div>

@if(isset($performanceData) && count($performanceData) > 0)
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get performance data from PHP
    const dataKelompok = @json($performanceData);
    
    console.log('Data Kelompok:', dataKelompok);
    
    if (!dataKelompok || dataKelompok.length === 0) {
        console.warn('No performance data available');
        return;
    }
    
    // Get current user's kelompok for highlighting
    const currentKelompok = @json(isset($kelompok) && $kelompok ? $kelompok->nama_kelompok : null);
    
    // Format data for ApexCharts horizontal bar
    const chartData = dataKelompok.map(item => ({
        x: item.nama,
        y: item.skor || 0
    }));
    
    console.log('Chart Data:', chartData);
    
    // Color: highlight current kelompok
    const colors = dataKelompok.map(item => {
        return item.nama === currentKelompok ? '#22c55e' : '#3b82f6';
    });
    
    // Check if ApexCharts is loaded
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts is not loaded');
        return;
    }
    
    const chartElement = document.getElementById('performanceRankingChart');
    if (!chartElement) {
        console.error('Chart element not found');
        return;
    }
    
    try {
        const options = {
            series: [{
                name: 'Skor Performa',
                data: chartData
            }],
            chart: {
                type: 'bar',
                height: 400,
                horizontal: true,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: {
                        position: 'right'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(2) + '/100';
                },
                style: {
                    fontSize: '12px',
                    colors: ['#1f2937']
                }
            },
            xaxis: {
                title: {
                    text: 'Skor Performa (0-100)'
                },
                min: 0,
                max: 100,
                labels: {
                    formatter: function(val) {
                        return val + '/100';
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Kelompok'
                }
            },
            colors: colors,
            tooltip: {
                y: {
                    formatter: function(val) {
                        return 'Skor: ' + val.toFixed(2) + '/100';
                    }
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4
            }
        };
        
        const chart = new ApexCharts(chartElement, options);
        chart.render();
        
        console.log('✅ ApexCharts initialized successfully with', chartData.length, 'groups');
    } catch (error) {
        console.error('❌ Error initializing ApexCharts:', error);
    }
});
</script>
@endif

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kelompokDashboard', () => ({
        stats: {},
        currentTime: '',
        loading: false,
        
        async init() {
            await this.loadStats();
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
            
            
            // Auto refresh every 5 minutes
            setInterval(() => {
                this.refreshData();
            }, 300000);
        },
        
        async loadStats() {
            try {
                const response = await fetch('/api/kelompok/dashboard-stats', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                this.stats = result.stats;
                console.log('Stats loaded:', this.stats);
                
                // Reinitialize lucide icons
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                    
                });
            } catch (error) {
                console.error('Error loading stats:', error);
                this.stats = {
                    nama_kelompok: 'Error',
                    shift: 'Error',
                    total_anggota: 0,
                    laporan_hari_ini: 0,
                    laporan_bulan_ini: 0,
                    avg_waktu_penyelesaian: 0
                };
            }
        },
        
        
        async refreshData() {
            this.loading = true;
            await this.loadStats();
            this.loading = false;
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
