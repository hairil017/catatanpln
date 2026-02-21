@extends('layouts.dashboard')

@section('title', 'Input Laporan')

@section('content')
<div class="p-3 sm:p-4 lg:p-6" x-data="laporanData()">
    <!-- Header -->
    <div class="mb-6 lg:mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 sm:p-8 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i data-lucide="file-text" class="w-8 h-8 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Input Laporan Kerja</h1>
                        <p class="text-blue-100 mt-1 text-sm sm:text-base">Kelola laporan kerja harian Anda dengan mudah dan efisien</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <button @click="showForm = true; resetForm()" 
                            class="flex items-center bg-white text-blue-600 px-4 sm:px-6 py-2.5 rounded-lg hover:bg-blue-50 transition-all duration-200 shadow-md hover:shadow-lg font-medium min-h-[44px]">
                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                        <span>Tambah Laporan</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Total Laporan</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['totalLaporan'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Laporan Hari Ini</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['laporanHariIni'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="calendar" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Laporan Bulan Ini</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['laporanBulanIni'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="message" x-transition class="mb-6">
        <div :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'" 
             class="border rounded-lg p-4">
            <div class="flex items-center">
                <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5 mr-3"></i>
                <span x-text="message"></span>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg mb-6 border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Filter Laporan</h3>
                    <p class="text-xs text-gray-500">Saring data laporan sesuai kebutuhan</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('kelompok.laporan') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Filter Tanggal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Tanggal
                    </label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ request('tanggal') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                </div>

                <!-- Filter Hari -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="calendar-days" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Hari
                    </label>
                    <select name="hari" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                        <option value="">Semua Hari</option>
                        <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                        <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                        <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                        <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                        <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                        <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                        <option value="Minggu" {{ request('hari') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                    </select>
                </div>

                <!-- Filter Nama -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="user" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Nama
                    </label>
                    <select name="nama" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                        <option value="">Semua Nama</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->nama }}" {{ request('nama') == $karyawan->nama ? 'selected' : '' }}>
                                {{ $karyawan->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Instansi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="building" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Instansi
                    </label>
                    <input type="text" 
                           name="instansi" 
                           value="{{ request('instansi') }}"
                           placeholder="Cari instansi..."
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all font-medium shadow-md hover:shadow-lg flex items-center justify-center">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('kelompok.laporan') }}" 
                       class="px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all font-medium shadow-md hover:shadow-lg flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Modal -->
    <div x-show="showForm" 
         x-transition
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4 backdrop-blur-sm"
         @click.self="closeForm()"
         @keydown.escape="closeForm()">
        <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-4xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i :data-lucide="editingId ? 'edit-2' : 'plus'" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900" x-text="editingId ? 'Edit Laporan' : 'Tambah Laporan Baru'"></h3>
                        <p class="text-sm text-gray-500" x-text="editingId ? 'Perbarui informasi laporan kerja' : 'Isi form di bawah untuk menambahkan laporan baru'"></p>
                    </div>
                </div>
                <button @click="closeForm()" 
                        class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            

            <form @submit.prevent="saveLaporan()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Hari -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="calendar-days" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Hari <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.hari" 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                                required>
                            <option value="">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               x-model="formData.tanggal"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               required>
                    </div>

                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="user" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.nama" 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                                required>
                            <option value="">Pilih Nama Karyawan</option>
                            @forelse($karyawans as $karyawan)
                                <option value="{{ $karyawan->nama }}">{{ $karyawan->nama }}</option>
                            @empty
                                <option value="" disabled>Tidak ada karyawan terdaftar di kelompok Anda</option>
                            @endforelse
                        </select>
                        <p class="text-xs text-gray-500 mt-1.5">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            Pilih nama karyawan dari kelompok Anda
                        </p>
                        <div x-show="errors.nama" class="mt-1 text-sm text-red-600" x-text="errors.nama"></div>
                    </div>

                    <!-- Instansi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="building" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Instansi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               x-model="formData.instansi"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               placeholder="Masukkan nama instansi"
                               required>
                        <div x-show="errors.instansi" class="mt-1 text-sm text-red-600" x-text="errors.instansi"></div>
                    </div>

                    <!-- Jam Masuk -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="clock" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Jam Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="time" 
                               step="1"
                               x-model="formData.jam_masuk"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               required>
                        <div x-show="errors.jam_masuk" class="mt-1 text-sm text-red-600" x-text="errors.jam_masuk"></div>
                    </div>

                    <!-- Jenis Kegiatan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="activity" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Jenis Kegiatan
                        </label>
                        <select x-model="formData.jenis_kegiatan" 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                            <option value="">Pilih Jenis Kegiatan</option>
                            <option value="Perbaikan Meteran">Perbaikan Meteran</option>
                            <option value="Perbaikan Sambungan Rumah">Perbaikan Sambungan Rumah</option>
                            <option value="Pemeriksaan Gardu">Pemeriksaan Gardu</option>
                            <option value="Jenis Kegiatan lainnya">Jenis Kegiatan lainnya</option>
                        </select>
                        <div x-show="errors.jenis_kegiatan" class="mt-1 text-sm text-red-600" x-text="errors.jenis_kegiatan"></div>
                    </div>

                    <!-- Deskripsi Kegiatan - Muncul untuk semua jenis kegiatan, wajib hanya untuk Jenis Kegiatan lainnya -->
                    <div class="md:col-span-2" x-show="formData.jenis_kegiatan" x-transition>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="file-text" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Deskripsi Kegiatan 
                            <span x-show="formData.jenis_kegiatan === 'Jenis Kegiatan lainnya'" class="text-red-500">*</span>
                        </label>
                        <textarea x-model="formData.deskripsi_kegiatan"
                                  rows="4"
                                  :required="formData.jenis_kegiatan === 'Jenis Kegiatan lainnya'"
                                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400 resize-none"
                                  :placeholder="formData.jenis_kegiatan === 'Jenis Kegiatan lainnya' 
                                    ? 'Masukkan deskripsi detail penanganan gangguan yang dilakukan, contoh: Pohon tumbang mengenai kabel listrik di Jl. Poros Galesong, dilakukan perbaikan dengan mengganti kabel yang rusak...'
                                    : 'Masukkan deskripsi detail kegiatan yang dilakukan (opsional)'"></textarea>
                        <p class="text-xs text-gray-500 mt-1.5" x-show="formData.jenis_kegiatan === 'Jenis Kegiatan lainnya'">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            Wajib diisi untuk jenis kegiatan Jenis Kegiatan lainnya
                        </p>
                        <p class="text-xs text-gray-500 mt-1.5" x-show="formData.jenis_kegiatan && formData.jenis_kegiatan !== 'Jenis Kegiatan lainnya'">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            Opsional - Anda dapat menambahkan deskripsi detail kegiatan jika diperlukan
                        </p>
                        <div x-show="errors.deskripsi_kegiatan" class="mt-1 text-sm text-red-600" x-text="errors.deskripsi_kegiatan"></div>
                    </div>

                    <!-- Waktu Mulai Kegiatan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="clock" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Waktu Mulai Kegiatan
                        </label>
                        <input type="time" 
                               step="1"
                               x-model="formData.waktu_mulai_kegiatan"
                               @change="calculateDuration()"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               placeholder="HH:MM">
                        <div x-show="errors.waktu_mulai_kegiatan" class="mt-1 text-sm text-red-600" x-text="errors.waktu_mulai_kegiatan"></div>
                    </div>

                    <!-- Waktu Selesai Kegiatan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="clock" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Waktu Selesai Kegiatan
                        </label>
                        <input type="time" 
                               step="1"
                               x-model="formData.waktu_selesai_kegiatan"
                               @change="calculateDuration()"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               placeholder="HH:MM">
                        <div x-show="errors.waktu_selesai_kegiatan" class="mt-1 text-sm text-red-600" x-text="errors.waktu_selesai_kegiatan"></div>
                        <p class="text-xs text-gray-500 mt-1.5">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            Durasi akan dihitung otomatis
                        </p>
                    </div>

                    <!-- Durasi Waktu (Read-only, dihitung otomatis) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="hourglass" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Durasi Waktu (jam)
                        </label>
                        <input type="text" 
                               x-model="formData.durasi_waktu_display"
                               readonly
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed"
                               placeholder="0.00">
                        <p class="text-xs text-gray-500 mt-1.5">Dihitung otomatis dari waktu mulai dan selesai</p>
                    </div>

                    <!-- Alamat Tujuan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Alamat Tujuan
                        </label>
                        <input type="text" 
                               x-model="formData.alamat_tujuan"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               placeholder="Masukkan alamat tujuan pekerjaan">
                        <div x-show="errors.alamat_tujuan" class="mt-1 text-sm text-red-600" x-text="errors.alamat_tujuan"></div>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="upload" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Foto/File Dokumentasi
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-200 cursor-pointer"
                         @click="document.getElementById('fileInput').click()">
                        <input type="file" 
                               id="fileInput"
                               @change="handleFileSelect($event)"
                               accept="image/*,.pdf"
                               class="hidden">
                        <div x-show="!selectedFile">
                            <div class="flex justify-center mb-3">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i data-lucide="upload" class="w-8 h-8 text-blue-600"></i>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-700 mb-1" x-text="currentFile ? 'Klik untuk upload foto/file baru (akan mengganti file lama)' : 'Klik untuk upload foto atau file dokumentasi'"></p>
                            <p class="text-xs text-gray-500">Format: JPG, PNG, PDF (Maksimal: 5MB)</p>
                        </div>
                        <div x-show="selectedFile" class="flex items-center justify-center space-x-3">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i data-lucide="file-check" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <p class="text-sm font-medium text-gray-900" x-text="selectedFile?.name"></p>
                                <p class="text-xs text-gray-500" x-text="(selectedFile?.size / 1024).toFixed(2) + ' KB'"></p>
                            </div>
                            <button type="button" 
                                    @click.stop="removeFile()"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <!-- Preview Image -->
                        <div x-show="selectedFile && selectedFile.type.startsWith('image/')" class="mt-4">
                            <img :src="previewImage" 
                                 alt="Preview" 
                                 class="max-w-full max-h-64 mx-auto rounded-lg border border-gray-200 shadow-sm">
                        </div>
                    </div>
                    <div x-show="currentFile && !selectedFile" class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i data-lucide="file" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="currentFile"></p>
                                    <p class="text-xs text-gray-500">File saat ini (klik area upload di atas untuk mengganti)</p>
                                </div>
                            </div>
                            <a :href="'/api/laporan-karyawan/' + editingId + '/download'" 
                               target="_blank"
                               class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-1">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                <span>Download</span>
                            </a>
                        </div>
                    </div>
                    <div x-show="selectedFile && currentFile" class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-yellow-600"></i>
                            <p class="text-xs text-yellow-700">File baru akan mengganti file lama saat disimpan</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                        Setelah upload foto, icon download akan otomatis aktif di tabel laporan
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-3 sm:space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" 
                            @click="closeForm()"
                            class="w-full sm:w-auto px-6 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all font-medium min-h-[44px]">
                        <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="loading"
                            class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px] shadow-md hover:shadow-lg font-medium">
                        <span x-show="loading" class="flex items-center justify-center">
                            <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>
                            Menyimpan...
                        </span>
                        <span x-show="!loading" class="flex items-center">
                            <i :data-lucide="editingId ? 'save' : 'check'" class="w-4 h-4 mr-2"></i>
                            <span x-text="editingId ? 'Perbarui Laporan' : 'Simpan Laporan'"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Deskripsi Jenis Kegiatan -->
    <div x-show="showDeskripsiModal" 
         x-transition
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4 backdrop-blur-sm"
         @click.self="showDeskripsiModal = false"
         @keydown.escape="showDeskripsiModal = false">
        <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-amber-100 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Deskripsi Jenis Kegiatan</h3>
                        <p class="text-sm text-gray-500">Detail kegiatan yang dilakukan</p>
                    </div>
                </div>
                <button @click="showDeskripsiModal = false" 
                        class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap" x-text="deskripsiGangguan"></p>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button @click="showDeskripsiModal = false"
                        class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all font-medium min-h-[44px]">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    <!-- Modal Import Excel -->
    <div x-show="showImportModal" 
         x-transition
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4 backdrop-blur-sm"
         @click.self="showImportModal = false"
         @keydown.escape="showImportModal = false">
        <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-lg transform transition-all">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="file-up" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Import Data Laporan</h3>
                        <p class="text-sm text-gray-500">Unggah file Excel untuk import massal</p>
                    </div>
                </div>
                <button @click="showImportModal = false" 
                        class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                    <h4 class="text-sm font-bold text-blue-800 mb-2 flex items-center">
                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                        Ketentuan Format Excel:
                    </h4>
                    <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                        <li>Kolom berurutan: Hari, Tanggal, Nama, Instansi, Jam Masuk, Waktu Mulai, Jenis Kegiatan, Deskripsi, Waktu Selesai, Alamat, Dokumentasi.</li>
                        <li>Deskripsi wajib diisi jika Jenis Kegiatan = "Jenis Kegiatan lainnya".</li>
                        <li>Durasi akan dihitung otomatis oleh sistem.</li>
                    </ul>
                </div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File Excel</label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-400 hover:bg-green-50 transition-all cursor-pointer"
                     @click="$refs.importFileInput.click()">
                    <input type="file" 
                           x-ref="importFileInput"
                           @change="importFile = $event.target.files[0]"
                           accept=".xlsx,.xls,.csv"
                           class="hidden">
                    <div x-show="!importFile">
                        <i data-lucide="upload-cloud" class="w-10 h-10 text-gray-400 mx-auto mb-2"></i>
                        <p class="text-sm text-gray-600">Klik untuk pilih file (.xlsx, .xls, .csv)</p>
                    </div>
                    <div x-show="importFile" class="flex items-center justify-center space-x-2">
                        <i data-lucide="file-check" class="w-6 h-6 text-green-600"></i>
                        <span class="text-sm font-medium text-gray-900" x-text="importFile?.name"></span>
                        <button @click.stop="importFile = null" class="text-red-500 hover:text-red-700">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button @click="showImportModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button @click="handleImport()"
                        :disabled="!importFile || importLoading"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all disabled:opacity-50 flex items-center">
                    <i x-show="importLoading" data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>
                    <span x-text="importLoading ? 'Memproses...' : 'Proses Import'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="list" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Daftar Laporan</h3>
                        <p class="text-xs text-gray-500">Semua laporan kerja yang telah Anda buat</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="/api/laporan-karyawan/template" 
                       class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors flex items-center space-x-2 shadow-sm">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Download Template</span>
                    </a>
                    <button @click="showImportModal = true" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2 shadow-md hover:shadow-lg">
                        <i data-lucide="file-up" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Import Excel</span>
                    </button>
                    <button @click="loadLaporans()" 
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center space-x-2 shadow-sm">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Refresh</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Hari/Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Instansi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu Mulai Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jenis Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu Selesai Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Durasi Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Alamat Tujuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dokumentasi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($laporans as $index => $laporan)
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                    {{ $laporans->firstItem() + $index }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $laporan->hari }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($laporan->tanggal)->locale('id')->isoFormat('DD MMM YYYY') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($laporan->nama)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $laporan->nama }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($laporan->instansi)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="building" class="w-4 h-4 text-blue-600"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $laporan->instansi }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                @if($laporan->jam_masuk)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-blue-600 flex-shrink-0"></i>
                                        <div class="truncate" title="{{ $laporan->jam_masuk }}">{{ Str::limit($laporan->jam_masuk, 50) }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($laporan->waktu_mulai_kegiatan)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($laporan->waktu_mulai_kegiatan)->format('H:i') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $laporan->jenis_kegiatan ?? '-' }}
                                    </span>
                                    @if($laporan->jenis_kegiatan === 'Jenis Kegiatan lainnya' && $laporan->deskripsi_kegiatan)
                                        <button type="button"
                                                @click="lihatDeskripsiGangguan('{{ $laporan->id }}', @js($laporan->deskripsi_kegiatan))"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg transition-all duration-200 hover:shadow-md"
                                                title="Lihat Deskripsi Jenis Kegiatan">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($laporan->waktu_selesai_kegiatan)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-green-600"></i>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($laporan->waktu_selesai_kegiatan)->format('H:i') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="hourglass" class="w-4 h-4 text-indigo-600"></i>
                                    <span class="font-medium">{{ number_format($laporan->durasi_waktu ?? 0, 2) }} jam</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($laporan->alamat_tujuan)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-blue-600 flex-shrink-0"></i>
                                        <div class="truncate" title="{{ $laporan->alamat_tujuan }}">{{ Str::limit($laporan->alamat_tujuan, 50) }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($laporan->file_path)
                                    <a href="/api/laporan-karyawan/{{ $laporan->id }}/download" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                                        <i data-lucide="file" class="w-4 h-4 mr-1"></i>
                                        <span class="text-xs">Lihat File</span>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Button -->
                                    <button type="button"
                                            @click.stop="bukaFormEdit('{{ $laporan->id }}')" 
                                            class="inline-flex items-center justify-center w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg"
                                            title="Edit Laporan">
                                        <i data-lucide="edit-2" class="w-5 h-5"></i>
                                    </button>
                                    
                                    <!-- Download Button -->
                                    @if($laporan->file_path)
                                    <a href="/api/laporan-karyawan/{{ $laporan->id }}/download" 
                                       target="_blank"
                                       class="inline-flex items-center justify-center w-10 h-10 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg"
                                       title="Download File">
                                        <i data-lucide="download" class="w-5 h-5"></i>
                                    </a>
                                    @else
                                    <button type="button"
                                            disabled
                                            class="inline-flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed opacity-50"
                                            title="Tidak ada file">
                                        <i data-lucide="download" class="w-5 h-5"></i>
                                    </button>
                                    @endif
                                    
                                    <!-- Delete Button -->
                                    <button type="button"
                                            @click.stop="deleteLaporan('{{ $laporan->id }}')" 
                                            class="inline-flex items-center justify-center w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg"
                                            title="Hapus Laporan">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-gray-100 rounded-full mb-4">
                                        <i data-lucide="file-text" class="w-16 h-16 text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-700 text-lg font-semibold mb-2">Belum ada laporan</p>
                                    <p class="text-gray-500 text-sm mb-4">Mulai dengan membuat laporan kerja pertama Anda</p>
                                    <button @click="showForm = true; resetForm()" 
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                        Tambah Laporan Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($laporans->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($laporans->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $laporans->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif
                
                @if($laporans->hasMorePages())
                    <a href="{{ $laporans->appends(request()->except('page'))->nextPageUrl() }}" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Selanjutnya
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                        Selanjutnya
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $laporans->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-medium">{{ $laporans->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-medium">{{ $laporans->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        @if($laporans->onFirstPage())
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </span>
                        @else
                            <a href="{{ $laporans->appends(request()->except('page'))->previousPageUrl() }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </a>
                        @endif
                        
                        @php
                            $currentPage = $laporans->currentPage();
                            $lastPage = $laporans->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp
                        
                        @if($start > 1)
                            <a href="{{ $laporans->appends(request()->except('page'))->url(1) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </a>
                            @if($start > 2)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                        @endif
                        
                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $currentPage)
                            <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $laporans->appends(request()->except('page'))->url($page) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $page }}
                            </a>
                            @endif
                        @endfor
                        
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                            <a href="{{ $laporans->appends(request()->except('page'))->url($lastPage) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $lastPage }}
                            </a>
                        @endif
                        
                        @if($laporans->hasMorePages())
                            <a href="{{ $laporans->appends(request()->except('page'))->nextPageUrl() }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </a>
                        @else
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laporanData', () => ({
        laporans: [],
        showForm: false,
        editingId: null,
        loading: false,
        message: '',
        messageType: '',
        formData: {
            hari: '',
            tanggal: new Date().toISOString().split('T')[0],
            nama: '',
            instansi: 'PLN Galesong',
            jam_masuk: '',
            jenis_kegiatan: '',
            deskripsi_kegiatan: '',
            waktu_mulai_kegiatan: '',
            waktu_selesai_kegiatan: '',
            durasi_waktu: 0,
            durasi_waktu_display: '0.00',
            alamat_tujuan: ''
        },
        errors: {},
        selectedFile: null,
        currentFile: null,
        previewImage: null,
        showImportModal: false,
        importFile: null,
        importLoading: false,
        showDeskripsiModal: false,
        deskripsiGangguan: '',
        
        async init() {
            // Load laporans saat halaman pertama kali dimuat
            await this.loadLaporans();
            
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        },
        
        async loadLaporans() {
            try {
                const response = await fetch('/api/laporan-karyawan', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                this.laporans = Array.isArray(result) ? result : [];
            } catch (error) {
                console.error('Error loading laporans:', error);
                this.showMessage('Gagal memuat data laporan: ' + error.message, 'error');
            }
        },
        
        calculateDuration() {
            if (this.formData.waktu_mulai_kegiatan && this.formData.waktu_selesai_kegiatan) {
                const [mulaiJam, mulaiMenit] = this.formData.waktu_mulai_kegiatan.split(':').map(Number);
                const [selesaiJam, selesaiMenit] = this.formData.waktu_selesai_kegiatan.split(':').map(Number);
                
                let mulaiTotal = mulaiJam * 60 + mulaiMenit;
                let selesaiTotal = selesaiJam * 60 + selesaiMenit;
                
                // Jika waktu selesai lebih kecil dari waktu mulai, berarti melewati tengah malam
                if (selesaiTotal < mulaiTotal) {
                    selesaiTotal += 24 * 60; // Tambah 24 jam
                }
                
                const durasiMenit = selesaiTotal - mulaiTotal;
                const durasiJam = durasiMenit / 60;
                
                this.formData.durasi_waktu = durasiJam;
                this.formData.durasi_waktu_display = durasiJam.toFixed(2);
            } else {
                this.formData.durasi_waktu = 0;
                this.formData.durasi_waktu_display = '0.00';
            }
        },
        
        async saveLaporan() {
            this.loading = true;
            this.errors = {};
            
            // Validate required fields
            if (!this.formData.hari || !this.formData.tanggal || !this.formData.nama || !this.formData.instansi || !this.formData.jam_masuk) {
                this.showMessage('Semua field wajib harus diisi!', 'error');
                this.loading = false;
                return;
            }
            
            // Validate deskripsi jika Jenis Kegiatan lainnya dipilih
            if (this.formData.jenis_kegiatan === 'Jenis Kegiatan lainnya' && !this.formData.deskripsi_kegiatan) {
                this.showMessage('Deskripsi Jenis Kegiatan lainnya wajib diisi!', 'error');
                this.loading = false;
                return;
            }
            
            try {
                const url = this.editingId ? `/api/laporan-karyawan/${this.editingId}` : '/api/laporan-karyawan';
                const formData = new FormData();
                
                formData.append('hari', this.formData.hari);
                formData.append('tanggal', this.formData.tanggal);
                formData.append('nama', this.formData.nama);
                formData.append('instansi', this.formData.instansi);
                formData.append('jam_masuk', this.formData.jam_masuk);
                formData.append('jenis_kegiatan', this.formData.jenis_kegiatan || '');
                formData.append('deskripsi_kegiatan', this.formData.deskripsi_kegiatan || '');
                formData.append('waktu_mulai_kegiatan', this.formData.waktu_mulai_kegiatan || '');
                formData.append('waktu_selesai_kegiatan', this.formData.waktu_selesai_kegiatan || '');
                formData.append('alamat_tujuan', this.formData.alamat_tujuan || '');
                
                if (this.selectedFile) {
                    formData.append('file', this.selectedFile);
                }
                
                if (this.editingId) {
                    formData.append('_method', 'PUT');
                }
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: formData
                });
                
                const result = await response.json();
                
                if (!response.ok) {
                    if (result.errors) {
                        this.errors = result.errors;
                    }
                    const errorMessage = result.message || result.error || 'Gagal menyimpan laporan';
                    throw new Error(errorMessage);
                }
                
                this.showMessage(this.editingId ? 'Laporan berhasil diperbarui!' : 'Laporan berhasil ditambahkan!', 'success');
                this.closeForm();
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                
            } catch (error) {
                console.error('Error saving laporan:', error);
                this.showMessage('Gagal menyimpan laporan: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async deleteLaporan(id) {
            const result = await SwalHelper.confirmDelete('⚠️ Konfirmasi Penghapusan', 'Apakah Anda yakin ingin menghapus laporan ini?');
            if (!result.isConfirmed) return;
            
            try {
                const response = await fetch(`/api/laporan-karyawan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                this.showMessage('Laporan berhasil dihapus!', 'success');
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
            } catch (error) {
                console.error('Error deleting laporan:', error);
                this.showMessage('Gagal menghapus laporan: ' + error.message, 'error');
            }
        },
        
        async bukaFormEdit(id) {
            try {
                this.loading = true;
                this.editingId = id;
                
                const response = await fetch(`/api/laporan-karyawan/${id}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (!result || !result.id) {
                    throw new Error('Data laporan tidak ditemukan');
                }
                
                // Parse tanggal
                let tanggalFormatted = '';
                if (result.tanggal) {
                    try {
                        const date = new Date(result.tanggal);
                        if (!isNaN(date.getTime())) {
                            tanggalFormatted = date.toISOString().split('T')[0];
                        } else {
                            tanggalFormatted = result.tanggal.split(' ')[0];
                        }
                    } catch (e) {
                        tanggalFormatted = result.tanggal.split(' ')[0] || '';
                    }
                }
                
                // Format waktu: pertahankan HH:mm:ss agar durasi tidak berubah saat simpan (durasi dihitung dari waktu_mulai & waktu_selesai saja)
                let waktuMulai = '';
                let waktuSelesai = '';
                if (result.waktu_mulai_kegiatan) {
                    const s = result.waktu_mulai_kegiatan.trim();
                    if (/^\d{1,2}:\d{2}:\d{2}$/.test(s)) {
                        waktuMulai = s;
                    } else if (/^\d{1,2}:\d{2}$/.test(s)) {
                        waktuMulai = s + ':00';
                    } else {
                        try {
                            const d = new Date('2000-01-01T' + s + 'Z');
                            if (!isNaN(d.getTime())) waktuMulai = d.toTimeString().slice(0, 8);
                            else waktuMulai = s;
                        } catch (e) {
                            waktuMulai = s;
                        }
                    }
                }
                if (result.waktu_selesai_kegiatan) {
                    const s = result.waktu_selesai_kegiatan.trim();
                    if (/^\d{1,2}:\d{2}:\d{2}$/.test(s)) {
                        waktuSelesai = s;
                    } else if (/^\d{1,2}:\d{2}$/.test(s)) {
                        waktuSelesai = s + ':00';
                    } else {
                        try {
                            const d = new Date('2000-01-01T' + s + 'Z');
                            if (!isNaN(d.getTime())) waktuSelesai = d.toTimeString().slice(0, 8);
                            else waktuSelesai = s;
                        } catch (e) {
                            waktuSelesai = s;
                        }
                    }
                }
                
                // Set form data
                this.formData = {
                    hari: result.hari || '',
                    tanggal: tanggalFormatted || new Date().toISOString().split('T')[0],
                    nama: result.nama || '',
                    instansi: result.instansi || 'PLN Galesong',
                    jam_masuk: result.jam_masuk || '',
                    jenis_kegiatan: result.jenis_kegiatan || '',
                    deskripsi_kegiatan: result.deskripsi_kegiatan || '',
                    waktu_mulai_kegiatan: waktuMulai,
                    waktu_selesai_kegiatan: waktuSelesai,
                    durasi_waktu: result.durasi_waktu !== null && result.durasi_waktu !== undefined ? Number(result.durasi_waktu) : 0,
                    durasi_waktu_display: result.durasi_waktu !== null && result.durasi_waktu !== undefined ? Number(result.durasi_waktu).toFixed(2) : '0.00',
                    alamat_tujuan: result.alamat_tujuan || ''
                };
                
                // Hitung durasi jika ada waktu
                if (waktuMulai && waktuSelesai) {
                    this.calculateDuration();
                }
                
                // Set current file
                if (result.file_path) {
                    const fileName = result.file_path.split('/').pop() || result.file_path;
                    this.currentFile = fileName;
                } else {
                    this.currentFile = null;
                }
                
                this.selectedFile = null;
                this.previewImage = null;
                
                const fileInput = document.getElementById('fileInput');
                if (fileInput) {
                    fileInput.value = '';
                }
                
                this.showForm = true;
                
                await this.$nextTick();
                
                setTimeout(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 200);
                
            } catch (error) {
                console.error('Error loading laporan for edit:', error);
                this.showMessage('Gagal memuat data laporan: ' + error.message, 'error');
                this.editingId = null;
                this.showForm = false;
            } finally {
                this.loading = false;
            }
        },
        
        closeForm() {
            this.showForm = false;
            this.editingId = null;
            this.selectedFile = null;
            this.currentFile = null;
            this.previewImage = null;
            this.resetForm();
        },
        
        resetForm() {
            this.formData = {
                hari: '',
                tanggal: new Date().toISOString().split('T')[0],
                nama: '',
                instansi: 'PLN Galesong',
                jam_masuk: '',
                jenis_kegiatan: '',
                deskripsi_kegiatan: '',
                waktu_mulai_kegiatan: '',
                waktu_selesai_kegiatan: '',
                durasi_waktu: 0,
                durasi_waktu_display: '0.00',
                alamat_tujuan: ''
            };
            this.errors = {};
            this.selectedFile = null;
            this.currentFile = null;
            this.previewImage = null;
            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    this.showMessage('File terlalu besar. Maksimal 5MB.', 'error');
                    return;
                }
                
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    this.showMessage('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.', 'error');
                    return;
                }
                
                this.selectedFile = file;
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previewImage = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.previewImage = null;
                }
            }
        },
        
        removeFile() {
            this.selectedFile = null;
            this.previewImage = null;
            document.getElementById('fileInput').value = '';
        },
        
        showMessage(text, type) {
            if (type === 'success') {
                SwalHelper.success('Berhasil 🎉', text);
            } else if (type === 'error') {
                SwalHelper.error('Gagal ❌', text);
            } else {
                Swal.fire({
                    text: text,
                    icon: type,
                    confirmButtonColor: '#f59e0b'
                });
            }
        },
        
        lihatDeskripsiGangguan(id, deskripsi) {
            this.deskripsiGangguan = deskripsi;
            this.showDeskripsiModal = true;
            
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        },

        async handleImport() {
            if (!this.importFile) return;
            
            this.importLoading = true;
            const formData = new FormData();
            formData.append('file', this.importFile);
            
            try {
                const response = await fetch('/api/laporan-karyawan/import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (!response.ok) {
                    let errorMessage = result.message || 'Gagal mengimport data';
                    if (result.errors && Array.isArray(result.errors)) {
                        errorMessage += ':\n' + result.errors.join('\n');
                    }
                    throw new Error(errorMessage);
                }
                
                this.showMessage(result.message, 'success');
                this.showImportModal = false;
                this.importFile = null;
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                
            } catch (error) {
                console.error('Import error:', error);
                Swal.fire({
                    title: 'Gagal Import ❌',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                this.importLoading = false;
            }
        }
    }));
});
</script>
@endsection