<?php

namespace App\Http\Controllers;

use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaporanKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base query
        $query = LaporanKaryawan::with('kelompok');
        
        // If user is karyawan, only show their group's reports
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }
        
        // Calculate statistics
        $totalLaporan = (clone $query)->count();
        $laporanHariIni = (clone $query)->whereDate('tanggal', today())->count();
        $laporanBulanIni = (clone $query)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
        
        // Apply filters
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }
        
        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        
        if ($request->filled('instansi')) {
            $query->where('instansi', 'like', '%' . $request->instansi . '%');
        }
        
        $laporans = $query->orderBy('tanggal', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
        
        $statistics = [
            'totalLaporan' => $totalLaporan,
            'laporanHariIni' => $laporanHariIni,
            'laporanBulanIni' => $laporanBulanIni,
        ];
        
        // Get karyawan from the same kelompok
        $karyawans = collect([]);
        if ($user->isKaryawan() && $user->kelompok_id) {
            $karyawans = Karyawan::where('kelompok_id', $user->kelompok_id)
                ->orderBy('nama', 'asc')
                ->get();
        }
        
        return view('dashboard.kelompok.laporan', compact('laporans', 'statistics', 'karyawans'));
    }
    
    public function getLaporans()
    {
        $user = Auth::user();
        $query = LaporanKaryawan::with('kelompok');
        
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }
        
        $laporans = $query->orderBy('created_at', 'desc')->get();
        return response()->json($laporans);
    }

    /**
     * Nilai kosong atau placeholder (--:--, --:--:--, dll) dianggap null agar validasi nullable/regex tidak error.
     */
    private function emptyTimeToNull($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $v = trim((string) $value);
        if ($v === '' || str_contains($v, '--')) {
            return null;
        }
        return $v;
    }

    /**
     * Normalisasi waktu ke H:i:s (untuk simpan ke DB). Menerima "H:i" atau "H:i:s".
     * Durasi HANYA dihitung dari waktu_mulai_kegiatan dan waktu_selesai_kegiatan (bukan jam_masuk).
     */
    private function normalizeTimeToHis(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = trim($value);
        if (preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/', $value)) {
            return $value;
        }
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $value)) {
            return $value . ':00';
        }
        return null;
    }

    /** Hitung durasi (jam) dari waktu_mulai dan waktu_selesai saja, presisi 6 desimal (sama seperti seeder/import). */
    private function calculateDurasiFromWaktuMulaiSelesai(?string $waktuMulai, ?string $waktuSelesai): float
    {
        $mulai = $this->normalizeTimeToHis($waktuMulai);
        $selesai = $this->normalizeTimeToHis($waktuSelesai);
        if (!$mulai || !$selesai) {
            return 0.0;
        }
        try {
            $m = \Carbon\Carbon::createFromFormat('H:i:s', $mulai);
            $s = \Carbon\Carbon::createFromFormat('H:i:s', $selesai);
            if ($s->lt($m)) {
                $s->addDay();
            }
            return round($m->diffInSeconds($s) / 3600, 6);
        } catch (\Throwable $e) {
            return 0.0;
        }
    }
    
    public function store(Request $request)
    {
        $request->merge([
            'waktu_mulai_kegiatan' => $this->emptyTimeToNull($request->waktu_mulai_kegiatan),
            'waktu_selesai_kegiatan' => $this->emptyTimeToNull($request->waktu_selesai_kegiatan),
        ]);
        $request->validate([
            'hari' => 'required|string',
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'jam_masuk' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:Perbaikan Meteran,Perbaikan Sambungan Rumah,Pemeriksaan Gardu,Jenis Kegiatan lainnya',
            'deskripsi_kegiatan' => 'nullable|string|required_if:jenis_kegiatan,Jenis Kegiatan lainnya',
            'waktu_mulai_kegiatan' => ['nullable', 'regex:#^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#'],
            'waktu_selesai_kegiatan' => ['nullable', 'regex:#^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#'],
            'alamat_tujuan' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'deskripsi_kegiatan.required_if' => 'Deskripsi Jenis Kegiatan lainnya wajib diisi ketika jenis kegiatan adalah Jenis Kegiatan lainnya.',
        ]);
        
        $user = Auth::user();
        
        if ($user->isKaryawan() && !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar dalam kelompok'
            ], 400);
        }
        
        // Durasi HANYA dari waktu_mulai_kegiatan & waktu_selesai_kegiatan (bukan jam_masuk), presisi 6 desimal
        $durasiWaktu = $this->calculateDurasiFromWaktuMulaiSelesai(
            $request->waktu_mulai_kegiatan,
            $request->waktu_selesai_kegiatan
        );
        $waktuMulaiHis = $this->normalizeTimeToHis($request->waktu_mulai_kegiatan);
        $waktuSelesaiHis = $this->normalizeTimeToHis($request->waktu_selesai_kegiatan);
        
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('laporan-dokumentasi', $fileName, 'public');
        }
        
        $laporan = LaporanKaryawan::create([
            'id' => Str::uuid(),
            'hari' => $request->hari,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'instansi' => $request->instansi,
            'jam_masuk' => $request->jam_masuk,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'waktu_mulai_kegiatan' => $waktuMulaiHis ?? $request->waktu_mulai_kegiatan,
            'waktu_selesai_kegiatan' => $waktuSelesaiHis ?? $request->waktu_selesai_kegiatan,
            'durasi_waktu' => $durasiWaktu,
            'alamat_tujuan' => $request->alamat_tujuan,
            'file_path' => $filePath,
            'kelompok_id' => $user->kelompok_id,
        ]);
        
        return response()->json($laporan->load('kelompok'));
    }
    
    public function show($id)
    {
        $laporan = LaporanKaryawan::with('kelompok')->findOrFail($id);
        return response()->json($laporan);
    }
    
    public function update(Request $request, $id)
    {
        $request->merge([
            'waktu_mulai_kegiatan' => $this->emptyTimeToNull($request->waktu_mulai_kegiatan),
            'waktu_selesai_kegiatan' => $this->emptyTimeToNull($request->waktu_selesai_kegiatan),
        ]);
        $request->validate([
            'hari' => 'required|string',
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'jam_masuk' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:Perbaikan Meteran,Perbaikan Sambungan Rumah,Pemeriksaan Gardu,Jenis Kegiatan lainnya',
            'deskripsi_kegiatan' => 'nullable|string',
            'waktu_mulai_kegiatan' => ['nullable', 'regex:#^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#'],
            'waktu_selesai_kegiatan' => ['nullable', 'regex:#^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#'],
            'alamat_tujuan' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        
        // Validasi khusus: deskripsi wajib jika jenis kegiatan adalah Jenis Kegiatan lainnya
        if ($request->jenis_kegiatan === 'Jenis Kegiatan lainnya' && empty($request->deskripsi_kegiatan)) {
            return response()->json([
                'success' => false,
                'message' => 'Deskripsi Jenis Kegiatan lainnya wajib diisi ketika jenis kegiatan adalah Jenis Kegiatan lainnya.',
                'errors' => [
                    'deskripsi_kegiatan' => ['Deskripsi Jenis Kegiatan lainnya wajib diisi.']
                ]
            ], 422);
        }
        
        $laporan = LaporanKaryawan::findOrFail($id);
        
        // Durasi HANYA dari waktu_mulai_kegiatan & waktu_selesai_kegiatan (bukan jam_masuk), presisi 6 desimal
        $durasiWaktu = $this->calculateDurasiFromWaktuMulaiSelesai(
            $request->waktu_mulai_kegiatan,
            $request->waktu_selesai_kegiatan
        );
        $waktuMulaiHis = $this->normalizeTimeToHis($request->waktu_mulai_kegiatan);
        $waktuSelesaiHis = $this->normalizeTimeToHis($request->waktu_selesai_kegiatan);
        
        $updateData = [
            'hari' => $request->hari,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'instansi' => $request->instansi,
            'jam_masuk' => $request->jam_masuk,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'waktu_mulai_kegiatan' => $waktuMulaiHis ?? $request->waktu_mulai_kegiatan,
            'waktu_selesai_kegiatan' => $waktuSelesaiHis ?? $request->waktu_selesai_kegiatan,
            'durasi_waktu' => $durasiWaktu,
            'alamat_tujuan' => $request->alamat_tujuan,
        ];
        
        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
                Storage::disk('public')->delete($laporan->file_path);
            }
            
            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('laporan-dokumentasi', $fileName, 'public');
            $updateData['file_path'] = $filePath;
        }
        
        $laporan->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui',
            'data' => $laporan->load('kelompok')
        ]);
    }
    
    public function destroy($id)
    {
        $laporan = LaporanKaryawan::findOrFail($id);
        
        // Delete file if exists
        if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
            Storage::disk('public')->delete($laporan->file_path);
        }
        
        $laporan->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function downloadFile($id)
    {
        $laporan = LaporanKaryawan::findOrFail($id);
        
        if (!$laporan->file_path || !Storage::disk('public')->exists($laporan->file_path)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }
        
        return Storage::disk('public')->download($laporan->file_path);
    }

    /**
     * Parse tanggal string format m/d/y (sama seperti PerbaikanMeteranKelompok1ManualSeeder)
     * agar import Excel konsisten dengan hasil seeder (1/10/2025 = 10 Januari, bukan 1 Oktober).
     */
    private function parseTanggalMdY(?string $value): ?string
    {
        if ($value === '' || $value === null) {
            return null;
        }
        $s = trim((string) $value);
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $s, $m)) {
            $month = (int) $m[1];
            $day = (int) $m[2];
            $year = (int) $m[3];
            if ($month >= 1 && $month <= 12 && $day >= 1 && $day <= 31) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }
        return null;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $user = Auth::user();
        if ($user->isKaryawan() && !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar dalam kelompok'
            ], 400);
        }

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            $importedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Structure: Hari, Tanggal, Nama, Instansi, Jam Masuk, Waktu Mulai, Jenis Kegiatan, Deskripsi, Waktu Selesai, Alamat, Dokumentasi
                // Index: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
                
                $data = [
                    'hari' => trim((string)($row[0] ?? '')),
                    'tanggal' => $row[1] ?? '',
                    'nama' => trim((string)($row[2] ?? '')),
                    'instansi' => trim((string)($row[3] ?? '')),
                    'jam_masuk' => $row[4] ?? '',
                    'jenis_kegiatan' => trim((string)($row[6] ?? '')),
                    'deskripsi_kegiatan' => trim((string)($row[7] ?? '')),
                    'waktu_selesai_kegiatan' => $row[8] ?? '',
                    'alamat_tujuan' => trim((string)($row[9] ?? '')),
                ];

                // Normalize Tanggal: Excel serial -> Y-m-d; string m/d/y -> parse seperti seeder (1/10/2025 = 10 Jan)
                $rawTanggal = $row[1] ?? '';
                if ($rawTanggal !== '' && $rawTanggal !== null) {
                    try {
                        if (is_numeric($rawTanggal)) {
                            $data['tanggal'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawTanggal)->format('Y-m-d');
                        } else {
                            $parsedMdY = $this->parseTanggalMdY($rawTanggal);
                            $data['tanggal'] = $parsedMdY ?? \Carbon\Carbon::parse(trim((string)$rawTanggal))->format('Y-m-d');
                        }
                    } catch (\Throwable $e) {
                        $data['tanggal'] = $this->parseTanggalMdY($rawTanggal) ?? trim((string)$rawTanggal);
                    }
                } else {
                    $data['tanggal'] = '';
                }

                // Normalize time columns (Jam Masuk, Waktu Mulai, Waktu Selesai): Excel serial -> H:i:s
                foreach (['jam_masuk' => 4, 'waktu_mulai_kegiatan' => 5, 'waktu_selesai_kegiatan' => 8] as $key => $col) {
                    $raw = $row[$col] ?? '';
                    if ($raw !== '' && $raw !== null) {
                        try {
                            if (is_numeric($raw)) {
                                $data[$key] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($raw)->format('H:i:s');
                            } else {
                                $data[$key] = \Carbon\Carbon::parse(trim((string)$raw))->format('H:i:s');
                            }
                        } catch (\Throwable $e) {
                            $data[$key] = trim((string)$raw);
                        }
                    } else {
                        $data[$key] = '';
                    }
                }

                // Basic validation
                if (!$data['hari'] || !$data['tanggal'] || !$data['nama'] || !$data['instansi'] || !$data['jam_masuk']) {
                    $errors[] = "Baris " . ($index + 2) . ": Data wajib (Hari, Tanggal, Nama, Instansi, Jam Masuk) tidak lengkap.";
                    continue;
                }

                // Normalize Jenis Kegiatan for comparison
                $jenisKegiatanLower = strtolower($data['jenis_kegiatan']);
                $isLainnya = ($jenisKegiatanLower === 'jenis kegiatan lainnya');

                // Validation for Deskripsi Kegiatan
                if ($isLainnya && empty($data['deskripsi_kegiatan'])) {
                    $errors[] = "Baris " . ($index + 2) . ": Deskripsi Kegiatan wajib diisi jika Jenis Kegiatan adalah 'Jenis Kegiatan lainnya'.";
                    continue;
                }

                // Map to exact enum values if possible
                $validJenis = [
                    'perbaikan meteran' => 'Perbaikan Meteran',
                    'perbaikan sambungan rumah' => 'Perbaikan Sambungan Rumah',
                    'pemeriksaan gardu' => 'Pemeriksaan Gardu',
                    'jenis kegiatan lainnya' => 'Jenis Kegiatan lainnya'
                ];
                
                if (isset($validJenis[$jenisKegiatanLower])) {
                    $data['jenis_kegiatan'] = $validJenis[$jenisKegiatanLower];
                }

                // Calculate Duration: jam desimal presisi 6 (sama seperti seeder). Pakai detik agar detik tidak hilang (diffInMinutes = integer).
                $durasiWaktu = 0;
                if ($data['tanggal'] && $data['waktu_mulai_kegiatan'] && $data['waktu_selesai_kegiatan']) {
                    try {
                        $waktuMulai = \Carbon\Carbon::parse($data['tanggal'] . ' ' . $data['waktu_mulai_kegiatan']);
                        $waktuSelesai = \Carbon\Carbon::parse($data['tanggal'] . ' ' . $data['waktu_selesai_kegiatan']);
                        if ($waktuSelesai->lt($waktuMulai)) {
                            $waktuSelesai->addDay();
                        }
                        $durasiWaktu = round($waktuMulai->diffInSeconds($waktuSelesai) / 3600, 6);
                    } catch (\Exception $e) {
                        $durasiWaktu = 0;
                    }
                }

                // Pastikan format simpan: tanggal Y-m-d, waktu H:i:s (sama seperti seeder)
                try {
                    if ($data['tanggal']) {
                        $data['tanggal'] = \Carbon\Carbon::parse($data['tanggal'])->format('Y-m-d');
                    }
                    if ($data['waktu_mulai_kegiatan']) {
                        $data['waktu_mulai_kegiatan'] = \Carbon\Carbon::parse($data['waktu_mulai_kegiatan'])->format('H:i:s');
                    }
                    if ($data['waktu_selesai_kegiatan']) {
                        $data['waktu_selesai_kegiatan'] = \Carbon\Carbon::parse($data['waktu_selesai_kegiatan'])->format('H:i:s');
                    }
                    if ($data['jam_masuk']) {
                        $data['jam_masuk'] = \Carbon\Carbon::parse($data['jam_masuk'])->format('H:i:s');
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": Format Tanggal atau Waktu tidak valid (" . $e->getMessage() . ").";
                    continue;
                }

                LaporanKaryawan::create([
                    'id' => Str::uuid(),
                    'hari' => $data['hari'],
                    'tanggal' => $data['tanggal'],
                    'nama' => $data['nama'],
                    'instansi' => $data['instansi'],
                    'jam_masuk' => $data['jam_masuk'],
                    'jenis_kegiatan' => $data['jenis_kegiatan'],
                    'deskripsi_kegiatan' => $data['deskripsi_kegiatan'],
                    'waktu_mulai_kegiatan' => $data['waktu_mulai_kegiatan'],
                    'waktu_selesai_kegiatan' => $data['waktu_selesai_kegiatan'],
                    'durasi_waktu' => $durasiWaktu,
                    'alamat_tujuan' => $data['alamat_tujuan'],
                    'kelompok_id' => $user->kelompok_id,
                ]);

                $importedCount++;
            }

            if ($importedCount === 0 && !empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengimport data.',
                    'errors' => $errors
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimport $importedCount data laporan.",
                'errors' => $errors // Include warnings if any
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Hari',
            'Tanggal',
            'Nama',
            'Instansi',
            'Jam Masuk',
            'Waktu Mulai Kegiatan',
            'Jenis Kegiatan',
            'Deskripsi Kegiatan',
            'Waktu Selesai Kegiatan',
            'Alamat Tujuan',
            'Dokumentasi'
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        // Add example row
        $example = [
            'Senin',
            '2025-01-01',
            'Nama Karyawan',
            'PLN Galesong',
            '08:00:00',
            '08:30:00',
            'Perbaikan Meteran',
            '',
            '09:30:00',
            'Alamat Contoh',
            ''
        ];
        foreach ($example as $index => $value) {
            $sheet->setCellValue([$index + 1, 2], $value);
        }

        // Auto-size columns
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'Template_Import_Laporan.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
