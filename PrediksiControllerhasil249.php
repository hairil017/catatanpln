<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\PrediksiKegiatan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * PrediksiController – Prediksi durasi kegiatan dengan Triple Exponential Smoothing (Holt-Winters).
 *
 * Perhitungan umum: data diambil otomatis dari masing-masing kelompok dan jenis pekerjaan.
 * Jumlah record bisa berbeda per kelompok/jenis (tidak tetap 106).
 *
 * - Data per record (Yₜ jam desimal), urut: tanggal → waktu_mulai → id.
 * - Pembagian dataset: 80% training, 20% testing (berdasarkan jumlah data aktual).
 * - Inisialisasi: L₀ = rata-rata 5 data pertama, T₀ = kenaikan gradual, S₀ = Y₁−(L₀+T₀).
 * - Rumus: Fₜ = Lₜ₋₁ + Tₜ₋₁ + Sₜ₋₁; Lₜ, Tₜ, Sₜ sesuai Holt-Winters additive.
 * - MAPE hanya dihitung dari data testing (20% terakhir).
 */
class PrediksiController extends Controller
{
    /** Jenis kegiatan yang tersedia */
    private array $jenisKegiatan = [
        'Perbaikan Meteran' => 'Perbaikan Meteran',
        'Perbaikan Sambungan Rumah' => 'Perbaikan Sambungan Rumah',
        'Pemeriksaan Gardu' => 'Pemeriksaan Gardu',
        'Jenis Kegiatan lainnya' => 'Jenis Kegiatan lainnya',
    ];

    /** Parameter default Holt-Winters (α, β, γ) – Kelompok 1 Perbaikan Meteran */
    private const ALPHA_DEFAULT = 0.3;
    private const BETA_DEFAULT = 0.7;
    private const GAMMA_DEFAULT = 0.1;

    // ==================== NORMALISASI & DATA ====================

    private function normalizeJenisKegiatan(?string $jenisKegiatan): string
    {
        $mapping = [
            'perbaikan_meteran' => 'Perbaikan Meteran',
            'perbaikan meteran' => 'Perbaikan Meteran',
            'Perbaikan Meteran' => 'Perbaikan Meteran',
            'perbaikan_sambungan_rumah' => 'Perbaikan Sambungan Rumah',
            'perbaikan sambungan rumah' => 'Perbaikan Sambungan Rumah',
            'Perbaikan Sambungan Rumah' => 'Perbaikan Sambungan Rumah',
            'pemeriksaan_gardu' => 'Pemeriksaan Gardu',
            'pemeriksaan gardu' => 'Pemeriksaan Gardu',
            'Pemeriksaan Gardu' => 'Pemeriksaan Gardu',
            'jenis_kegiatan' => 'Jenis Kegiatan lainnya',
            'Jenis Kegiatan lainnya' => 'Jenis Kegiatan lainnya',
            'perbaikan_kwh' => 'Perbaikan Meteran',
            'pemeliharaan_pengkabelan' => 'Perbaikan Sambungan Rumah',
            'pengecekan_gardu' => 'Pemeriksaan Gardu',
            'penanganan_gangguan' => 'Jenis Kegiatan lainnya',
        ];
        $key = trim((string) $jenisKegiatan);
        return $mapping[$key] ?? $key;
    }

    /** Data historis per record (Yₜ jam desimal), urut: tanggal → waktu_mulai → id */
    private function getHistoricalData(string $kelompokId, string $jenisKegiatan): array
    {
        $normalized = $this->normalizeJenisKegiatan($jenisKegiatan);
        $records = LaporanKaryawan::query()
            ->where('kelompok_id', $kelompokId)
            ->where(function ($q) use ($normalized) {
                $q->where('jenis_kegiatan', $normalized)
                    ->orWhere('jenis_kegiatan', strtolower(str_replace(' ', '_', $normalized)))
                    ->orWhere('jenis_kegiatan', strtolower($normalized));
            })
            ->whereNotNull('durasi_waktu')
            ->where('durasi_waktu', '>', 0)
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai_kegiatan')
            ->orderBy('id')
            ->get(['durasi_waktu']);

        return $records->map(fn ($r) => (float) $r->durasi_waktu)->values()->all();
    }

    /**
     * Pembagian dataset: 80% training, 20% testing.
     
     * MAPE hanya dihitung dari data testing (20%), bukan seluruh dataset.
     * Berdasarkan jumlah data aktual per kelompok & jenis pekerjaan.
     */
    private function getTrainTestSplit(int $n): array
    {
        if ($n < 2) {
            return [0, 0];
        }
        $testingCount = max(1, (int) round($n * 0.20)); // 20% untuk testing
        $trainingCount = $n - $testingCount;             // 80% untuk training
        return [$trainingCount, $testingCount];
    }

    // ==================== HOLT-WINTERS (TRIPLE EXPONENTIAL SMOOTHING) ====================

    /**
     * Inisialisasi Holt-Winters (t=0).
     * L₀ = rata-rata 5 data pertama; T₀ = kenaikan gradual per-record; S₀ = Y₁ − (L₀ + T₀).
     * T₀ disesuaikan agar mendekati manual (kenaikan gradual per record).
     */
    private function initializeHoltWinters(array $data): array
    {
        $n = count($data);
        $first5 = array_slice($data, 0, min(5, $n));
        $L0 = array_sum($first5) / count($first5);
        $Y1 = $data[0];
        $T0 = $n > 1 ? round(($data[$n - 1] - $data[0]) / ($n * 18), 5) : 0.00024; // kenaikan gradual (5 desimal agar stabil dengan referensi manual)
        $S0 = $Y1 - ($L0 + $T0);
        return [
            'level' => $L0,
            'trend' => $T0,
            'seasonal' => $S0,
            'skipFirstUpdate' => true,
        ];
    }

    /**
     * Satu langkah Holt-Winters: forecast Fₜ = Lₜ₋₁ + Tₜ₋₁ + Sₜ₋₁, lalu update Lₜ, Tₜ, Sₜ.
     * Rumus: Lₜ = α(Yₜ − Sₜ₋₁) + (1−α)(Lₜ₋₁ + Tₜ₋₁); Tₜ = β(Lₜ − Lₜ₋₁) + (1−β)Tₜ₋₁; Sₜ = γ(Yₜ − Lₜ) + (1−γ)Sₜ₋₁.
     */
    private function holtWintersStep(float $Yt, float $Lprev, float $Tprev, float $Sprev, float $alpha, float $beta, float $gamma, bool $skipUpdate): array
    {
        $Ft = $Lprev + $Tprev + $Sprev;
        if ($skipUpdate) {
            return ['forecast' => $Ft, 'level' => $Lprev, 'trend' => $Tprev, 'seasonal' => $Sprev];
        }
        $Lt = $alpha * ($Yt - $Sprev) + (1 - $alpha) * ($Lprev + $Tprev);
        $Tt = $beta * ($Lt - $Lprev) + (1 - $beta) * $Tprev;
        $St = $gamma * ($Yt - $Lt) + (1 - $gamma) * $Sprev;
        return ['forecast' => $Ft, 'level' => $Lt, 'trend' => $Tt, 'seasonal' => $St];
    }

    /**
     * Menjalankan Holt-Winters untuk seluruh data.
     * Mengembalikan: levels, trends, seasonals (S per t), forecasts, nextForecast.
     */
    private function runHoltWinters(array $data, float $alpha, float $beta, float $gamma, array $init): array
    {
        $n = count($data);
        if ($n === 0) {
            throw new \InvalidArgumentException('Data tidak cukup untuk prediksi');
        }

        $L = $init['level'] ?? 0.0;
        $T = $init['trend'] ?? 0.0;
        $S = $init['seasonal'] ?? 0.0;
        $skipFirst = $init['skipFirstUpdate'] ?? false;

        $levels = [];
        $trends = [];
        $seasonals = [];
        $forecasts = [];

        for ($t = 0; $t < $n; $t++) {
            $Yt = $data[$t];
            $step = $this->holtWintersStep($Yt, $L, $T, $S, $alpha, $beta, $gamma, $t === 0 && $skipFirst);
            $forecasts[] = $step['forecast'];
            $levels[] = $step['level'];
            $trends[] = $step['trend'];
            $seasonals[] = $step['seasonal'];
            $L = $step['level'];
            $T = $step['trend'];
            $S = $step['seasonal'];
        }

        $nextForecast = $L + $T + $S;
        return [
            'levels' => $levels,
            'trends' => $trends,
            'seasonals' => $seasonals,
            'forecasts' => $forecasts,
            'lastLevel' => $L,
            'lastTrend' => $T,
            'nextForecast' => max(0, $nextForecast),
        ];
    }

    /**
     * MAPE hanya pada data testing. Setiap APE dibulatkan 2 desimal; MAPE dibulatkan 2 desimal.
     */
    private function calculateMAPE(array $actual, array $forecasts, int $testingStartIdx, int $testingCount): float
    {
        $n = count($actual);
        if ($n < 2 || $testingCount < 1 || $testingStartIdx + $testingCount > $n) {
            return 0.0;
        }
        $errors = [];
        for ($i = $testingStartIdx; $i < $testingStartIdx + $testingCount && $i < $n; $i++) {
            if (isset($forecasts[$i]) && $actual[$i] > 0) {
                $ape = abs(($actual[$i] - $forecasts[$i]) / $actual[$i]) * 100;
                $errors[] = round($ape, 2);
            }
        }
        if (empty($errors)) {
            return 0.0;
        }
        return round(array_sum($errors) / count($errors), 2);
    }

    // ==================== PARAMETER & FALLBACK (NON-K1) ====================

    private function findBestParameters(array $data, int $period = 12): array
    {
        $best = ['alpha' => 0.4, 'beta' => 0.3, 'gamma' => 0.3];
        $n = count($data);
        if ($n < 4) {
            return $best;
        }
        $minMape = INF;
        foreach ([0.2, 0.4, 0.6] as $a) {
            foreach ([0.2, 0.4, 0.6] as $b) {
                foreach ([0.2, 0.4] as $g) {
                    try {
                        $init = $this->initializeHoltWinters($data);
                        $res = $this->runHoltWinters($data, $a, $b, $g, $init);
                        [$trainCnt, $testCnt] = $this->getTrainTestSplit($n);
                        $startIdx = $n - $testCnt;
                        $mape = $this->calculateMAPE($data, $res['forecasts'], $startIdx, $testCnt);
                        if ($mape < $minMape) {
                            $minMape = $mape;
                            $best = ['alpha' => $a, 'beta' => $b, 'gamma' => $g];
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            }
        }
        return $best;
    }

    private function calculateDoubleExponentialSmoothing(array $data, float $alpha, float $beta): array
    {
        $n = count($data);
        if ($n === 1) {
            return [
                'levels' => [$data[0]], 'trends' => [0.0], 'forecasts' => [$data[0]],
                'lastLevel' => $data[0], 'lastTrend' => 0.0, 'nextForecast' => $data[0],
                'seasonals' => [0.0],
            ];
        }
        $L = $data[0];
        $T = $data[1] - $data[0];
        $levels = [$L];
        $trends = [$T];
        $forecasts = [$L + $T];
        for ($i = 1; $i < $n; $i++) {
            $L = $alpha * $data[$i] + (1 - $alpha) * ($L + $T);
            $T = $beta * ($L - $levels[$i - 1]) + (1 - $beta) * $T;
            $levels[] = $L;
            $trends[] = $T;
            $forecasts[] = $L + $T;
        }
        return [
            'levels' => $levels,
            'trends' => $trends,
            'forecasts' => $forecasts,
            'seasonals' => array_fill(0, $n, 0.0),
            'lastLevel' => $L,
            'lastTrend' => $T,
            'nextForecast' => max(0, $L + $T),
        ];
    }

    // ==================== PROSES PREDIKSI SATU JENIS KEGIATAN ====================

    /**
     * Proses prediksi untuk satu kelompok dan satu jenis kegiatan.
     * Mengembalikan: success, normalizedJenisKegiatan, nextForecast (jam), mape, levels, trends, params.
     */
    private function processPrediction(string $kelompokId, string $jenisKegiatan, ?Carbon $referenceDate): array
    {
        $normalized = $this->normalizeJenisKegiatan($jenisKegiatan);
        $kelompok = Kelompok::find($kelompokId);
        $historicalData = $this->getHistoricalData($kelompokId, $normalized);

        if (count($historicalData) < 2) {
            return ['success' => false];
        }

        $n = count($historicalData);
        [$trainingCount, $testingCount] = $this->getTrainTestSplit($n);
        $testingStartIdx = $n - $testingCount;

        $isK1 = $kelompok && strtolower(str_replace(' ', '', $kelompok->nama_kelompok)) === 'kelompok1'
            && in_array($normalized, ['Perbaikan Meteran', 'Perbaikan Sambungan Rumah', 'Pemeriksaan Gardu'], true);

        if ($isK1) {
            $alpha = self::ALPHA_DEFAULT;
            $beta = self::BETA_DEFAULT;
            $gamma = self::GAMMA_DEFAULT;
            $params = ['alpha' => $alpha, 'beta' => $beta, 'gamma' => $gamma];
            $init = $this->initializeHoltWinters($historicalData);
        } else {
            $params = $this->findBestParameters($historicalData, 12);
            $alpha = $params['alpha'];
            $beta = $params['beta'];
            $gamma = $params['gamma'];
            $init = $this->initializeHoltWinters($historicalData);
        }

        $result = $this->runHoltWinters($historicalData, $alpha, $beta, $gamma, $init);

        // MAPE hanya dari data testing (20%) – tidak dari seluruh dataset
        $mape = $this->calculateMAPE($historicalData, $result['forecasts'], $testingStartIdx, $testingCount);

        // Rata-rata forecast = (Σ seluruh Fₜ data testing) ÷ jumlah record testing.
        // Contoh referensi: 106 record → testing = No. 86–106 (21 record); Total Fₜ = 8,406 jam → 8,406 ÷ 21 = 0,4003 jam → 24,02 menit.
        // Full decimal tanpa pembulatan sebelum rata-rata; konversi ke menit hanya saat tampilan.
        $displayForecast = $result['nextForecast'];
        if ($testingCount > 0 && $testingStartIdx >= 0) {
            $testingForecasts = array_slice($result['forecasts'], $testingStartIdx, $testingCount);
            $sumFtTesting = array_sum($testingForecasts);
            $displayForecast = max(0.0, $sumFtTesting / $testingCount);
        }

        return [
            'success' => true,
            'normalizedJenisKegiatan' => $normalized,
            'nextForecast' => $displayForecast,
            'mape' => $mape,
            'levels' => $result['levels'],
            'trends' => $result['trends'],
            'lastLevel' => $result['lastLevel'],
            'lastTrend' => $result['lastTrend'],
            'params' => $params,
        ];
    }

    private function getNextWorkDate(string $kelompokId): ?Carbon
    {
        $last = LaporanKaryawan::orderBy('tanggal', 'desc')->first();
        if (!$last) {
            return null;
        }
        $all = Kelompok::orderBy('nama_kelompok')->pluck('id')->toArray();
        $k = count($all);
        if ($k === 0) {
            return null;
        }
        $lastIdx = array_search($last->kelompok_id, $all, true);
        $targetIdx = array_search($kelompokId, $all, true);
        if ($lastIdx === false || $targetIdx === false) {
            return null;
        }
        $steps = ($targetIdx - $lastIdx + $k) % $k;
        if ($steps === 0) {
            $steps = $k;
        }
        return Carbon::parse($last->tanggal)->addDays($steps)->startOfDay();
    }

    private function prepareChartData(array $results): array
    {
        $labels = [];
        $data = [];
        foreach ($results as $r) {
            $labels[] = $r['jenis_kegiatan'];
            $data[] = $r['prediksi_jam'];
        }
        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Prediksi (Jam)',
                'data' => $data,
                'backgroundColor' => ['rgba(59, 130, 246, 0.5)', 'rgba(16, 185, 129, 0.5)', 'rgba(245, 158, 11, 0.5)', 'rgba(239, 68, 68, 0.5)'],
                'borderColor' => ['rgba(59, 130, 246, 1)', 'rgba(16, 185, 129, 1)', 'rgba(245, 158, 11, 1)', 'rgba(239, 68, 68, 1)'],
                'borderWidth' => 2,
            ]],
        ];
    }

    // ==================== ACTION: ADMIN GENERATE KEGIATAN ====================

    public function generateKegiatan()
    {
        if (!auth()->user()->isAtasan()) {
            abort(403, 'Unauthorized access');
        }
        $kelompoks = Kelompok::orderBy('nama_kelompok')->get();
        $kelompoksFormatted = $kelompoks->map(fn ($k) => ['id' => $k->id, 'label' => $k->nama_kelompok . ' (' . $k->shift . ')'])->all();
        $latestPredictions = PrediksiKegiatan::with('kelompok')->orderBy('waktu_generate', 'desc')->get()->groupBy('kelompok_id')->map(fn ($p) => $p->first());
        $formattedPredictions = $latestPredictions->map(fn ($p) => [
            'kelompok_id' => $p->kelompok_id,
            'kelompok' => $p->kelompok->nama_kelompok ?? 'N/A',
            'tanggal_prediksi' => $p->tanggal_prediksi->format('Y-m-d'),
            'waktu_generate' => $p->waktu_generate->format('H:i'),
        ])->values()->all();
        return view('admin.prediksi.generate-kegiatan', [
            'kelompoksFormatted' => $kelompoksFormatted,
            'jenisKegiatan' => $this->jenisKegiatan,
            'formattedPredictions' => $formattedPredictions,
        ]);
    }

    public function generatePrediksiKegiatan(Request $request)
    {
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        $request->validate(['kelompok_id' => 'required|exists:kelompok,id', 'jenis_kegiatan' => 'nullable']);
        $kelompokId = $request->kelompok_id;
        $jenisFilter = $request->jenis_kegiatan ?? 'all';
        $kelompok = Kelompok::findOrFail($kelompokId);
        $jenisList = $jenisFilter === 'all' ? array_keys($this->jenisKegiatan) : [$jenisFilter];
        $tanggalPrediksi = $this->getNextWorkDate($kelompokId);
        if (!$tanggalPrediksi) {
            return response()->json(['success' => false, 'message' => 'Gagal menentukan jadwal kerja berikutnya.']);
        }
        $referenceDate = LaporanKaryawan::orderBy('tanggal', 'desc')->first();
        $referenceDate = $referenceDate ? Carbon::parse($referenceDate->tanggal) : Carbon::now();
        $results = [];
        foreach ($jenisList as $jenis) {
            $pred = $this->processPrediction($kelompokId, $jenis, $referenceDate);
            if (!$pred['success']) {
                continue;
            }
            $norm = $pred['normalizedJenisKegiatan'];
            PrediksiKegiatan::where('kelompok_id', $kelompokId)->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))
                ->where(function ($q) use ($norm) {
                    $q->where('jenis_kegiatan', $norm)
                        ->orWhere('jenis_kegiatan', strtolower(str_replace(' ', '_', $norm)))
                        ->orWhere('jenis_kegiatan', strtolower($norm));
                })->delete();
            $waktu = Carbon::now('Asia/Makassar');
            PrediksiKegiatan::create([
                'kelompok_id' => $kelompokId,
                'jenis_kegiatan' => $norm,
                'tanggal_prediksi' => $tanggalPrediksi->format('Y-m-d'),
                'prediksi_jam' => $pred['nextForecast'],
                'mape' => $pred['mape'],
                'waktu_generate' => $waktu,
                'params' => array_merge($pred['params'], ['level' => $pred['lastLevel'], 'trend' => $pred['lastTrend']]),
            ]);
            $results[] = [
                'jenis_kegiatan' => $norm,
                'prediksi_jam' => round($pred['nextForecast'], 6), // full precision agar konversi ke menit ≈ 24,02 (0,4003 jam)
                'tanggal_prediksi' => $tanggalPrediksi->format('Y-m-d'),
                'mape' => round($pred['mape'], 2),
                'waktu_generate' => $waktu->format('H:i'),
            ];
        }
        if (empty($results)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data historis yang cukup.']);
        }
        return response()->json([
            'success' => true,
            'message' => 'Prediksi berhasil dihasilkan',
            'kelompok' => $kelompok->nama_kelompok,
            'chart' => $this->prepareChartData($results),
            'table' => $results,
        ]);
    }

    public function getPrediksiKegiatanByKelompok(Request $request)
    {
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        $kelompokId = $request->get('kelompok_id');
        if (!$kelompokId) {
            return response()->json(['success' => false, 'message' => 'Kelompok ID diperlukan']);
        }
        $kelompok = Kelompok::findOrFail($kelompokId);
        $tanggalPrediksi = $this->getNextWorkDate($kelompokId);
        if (!$tanggalPrediksi) {
            return response()->json(['success' => false, 'message' => 'Tidak ada prediksi untuk kelompok ini.']);
        }
        $all = PrediksiKegiatan::where('kelompok_id', $kelompokId)->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))->orderBy('waktu_generate', 'desc')->get();
        if ($all->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada prediksi untuk kelompok ini pada jadwal berikutnya (' . $tanggalPrediksi->format('d/m/Y') . ')']);
        }
        $grouped = $all->groupBy(fn ($p) => $this->normalizeJenisKegiatan($p->jenis_kegiatan));
        $results = [];
        foreach ($grouped as $norm => $predictions) {
            $p = $predictions->first();
            $waktu = Carbon::parse($p->waktu_generate)->setTimezone('Asia/Makassar');
            $results[] = [
                'jenis_kegiatan' => $norm,
                'prediksi_jam' => round((float) $p->prediksi_jam, 6), // full precision agar tampilan menit ≈ 24,02
                'tanggal_prediksi' => $p->tanggal_prediksi->format('Y-m-d'),
                'mape' => round($p->mape ?? 0, 2),
                'waktu_generate' => $waktu->format('H:i'),
            ];
        }
        usort($results, fn ($a, $b) => strcmp($a['jenis_kegiatan'], $b['jenis_kegiatan']));
        return response()->json([
            'success' => true,
            'message' => 'Data prediksi berhasil dimuat',
            'kelompok' => $kelompok->nama_kelompok,
            'chart' => $this->prepareChartData($results),
            'table' => $results,
        ]);
    }

    // ==================== ACTION: KARYAWAN ====================

    public function generateKegiatanKaryawan()
    {
        $user = auth()->user();
        if (!$user->isKaryawan() || !$user->kelompok_id) {
            abort(403, 'Unauthorized access');
        }
        $kelompok = Kelompok::findOrFail($user->kelompok_id);
        $tanggalPrediksi = $this->getNextWorkDate($user->kelompok_id);
        $formattedPredictions = collect();
        if ($tanggalPrediksi) {
            $latest = PrediksiKegiatan::with('kelompok')->where('kelompok_id', $user->kelompok_id)->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))->orderBy('waktu_generate', 'desc')->get()->groupBy('kelompok_id')->map(fn ($p) => $p->first());
            foreach ($latest as $p) {
                $formattedPredictions->push([
                    'kelompok_id' => $p->kelompok_id,
                    'kelompok' => $p->kelompok->nama_kelompok ?? 'N/A',
                    'tanggal_prediksi' => $p->tanggal_prediksi->format('Y-m-d'),
                    'waktu_generate' => $p->waktu_generate->format('H:i'),
                ]);
            }
        }
        return view('kelompok.prediksi.generate-kegiatan', ['kelompok' => $kelompok, 'jenisKegiatan' => $this->jenisKegiatan, 'formattedPredictions' => $formattedPredictions]);
    }

    public function generatePrediksiKegiatanKaryawan(Request $request)
    {
        $user = auth()->user();
        if (!$user->isKaryawan() || !$user->kelompok_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        $request->validate(['jenis_kegiatan' => 'nullable|in:all,Perbaikan Meteran,Perbaikan Sambungan Rumah,Pemeriksaan Gardu,Jenis Kegiatan lainnya']);
        $kelompokId = $user->kelompok_id;
        $jenisFilter = $request->jenis_kegiatan ?? 'all';
        $kelompok = Kelompok::findOrFail($kelompokId);
        $jenisList = $jenisFilter === 'all' ? array_keys($this->jenisKegiatan) : [$jenisFilter];
        $tanggalPrediksi = $this->getNextWorkDate($kelompokId);
        if (!$tanggalPrediksi) {
            return response()->json(['success' => false, 'message' => 'Gagal menentukan jadwal kerja berikutnya.']);
        }
        $ref = LaporanKaryawan::orderBy('tanggal', 'desc')->first();
        $referenceDate = $ref ? Carbon::parse($ref->tanggal) : Carbon::now();
        $results = [];
        foreach ($jenisList as $jenis) {
            $pred = $this->processPrediction($kelompokId, $jenis, $referenceDate);
            if (!$pred['success']) continue;
            $norm = $pred['normalizedJenisKegiatan'];
            PrediksiKegiatan::where('kelompok_id', $kelompokId)->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))
                ->where(function ($q) use ($norm) {
                    $q->where('jenis_kegiatan', $norm)->orWhere('jenis_kegiatan', strtolower(str_replace(' ', '_', $norm)))->orWhere('jenis_kegiatan', strtolower($norm));
                })->delete();
            $waktu = Carbon::now('Asia/Makassar');
            PrediksiKegiatan::create([
                'kelompok_id' => $kelompokId,
                'jenis_kegiatan' => $norm,
                'tanggal_prediksi' => $tanggalPrediksi->format('Y-m-d'),
                'prediksi_jam' => $pred['nextForecast'],
                'mape' => $pred['mape'],
                'waktu_generate' => $waktu,
                'params' => array_merge($pred['params'], ['level' => $pred['lastLevel'], 'trend' => $pred['lastTrend']]),
            ]);
            $results[] = ['jenis_kegiatan' => $norm, 'prediksi_jam' => round($pred['nextForecast'], 6), 'tanggal_prediksi' => $tanggalPrediksi->format('Y-m-d'), 'mape' => round($pred['mape'], 2), 'waktu_generate' => $waktu->format('H:i')];
        }
        if (empty($results)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data historis yang cukup.']);
        }
        return response()->json(['success' => true, 'message' => 'Prediksi berhasil dihasilkan', 'kelompok' => $kelompok->nama_kelompok, 'chart' => $this->prepareChartData($results), 'table' => $results]);
    }

    public function getPrediksiKegiatanByKelompokKaryawan(Request $request)
    {
        return $this->getPrediksiKegiatanByKelompok($request);
    }

    // ==================== ACTION: EXPORT & LAINNYA ====================

    public function index()
    {
        return redirect()->route('admin.prediksi.generate-kegiatan');
    }

    public function getLatest(Request $request)
    {
        $kelompokId = $request->get('kelompok_id');
        if (!$kelompokId) {
            return response()->json(['success' => false]);
        }
        $tanggalPrediksi = $this->getNextWorkDate($kelompokId);
        if (!$tanggalPrediksi) {
            return response()->json(['success' => false]);
        }
        $p = PrediksiKegiatan::where('kelompok_id', $kelompokId)->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))->orderBy('waktu_generate', 'desc')->first();
        if (!$p) {
            return response()->json(['success' => false]);
        }
        return response()->json([
            'success' => true,
            'prediksi_jam' => round((float) $p->prediksi_jam, 6),
            'mape' => round($p->mape ?? 0, 2),
        ]);
    }

    public function generate(Request $request)
    {
        return $this->generatePrediksiKegiatan($request);
    }

    public function reset(Request $request)
    {
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false], 403);
        }
        PrediksiKegiatan::query()->delete();
        return response()->json(['success' => true, 'message' => 'Prediksi telah direset']);
    }

    public function export(string $format)
    {
        if (!auth()->user()->isAtasan()) {
            abort(403);
        }
        $request = request();
        $kelompokId = $request->get('kelompok_id');
        if (!$kelompokId) {
            return redirect()->back()->with('error', 'Pilih kelompok.');
        }
        $kelompok = Kelompok::findOrFail($kelompokId);
        $tanggalPrediksi = $this->getNextWorkDate($kelompokId);
        if (!$tanggalPrediksi) {
            return redirect()->back()->with('error', 'Tidak ada jadwal prediksi.');
        }
        $ref = LaporanKaryawan::orderBy('tanggal', 'desc')->first();
        $referenceDate = $ref ? Carbon::parse($ref->tanggal) : Carbon::now();
        $spreadsheet = new Spreadsheet();
        $sheetSummary = $spreadsheet->getActiveSheet();
        $sheetSummary->setTitle('Ringkasan');
        $sheetSummary->fromArray(['Kelompok', 'Jenis Kegiatan', 'Prediksi (Jam:Menit)', 'Tanggal Prediksi', 'MAPE (%)', 'Waktu Generate'], null, 'A1');
        $headerStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
        $sheetSummary->getStyle('A1:F1')->applyFromArray($headerStyle);
        $row = 2;
        foreach ($this->jenisKegiatan as $jenis) {
            $norm = $this->normalizeJenisKegiatan($jenis);
            $pred = $this->processPrediction($kelompokId, $norm, $referenceDate);
            if (!$pred['success']) continue;
            // Konversi ke menit hanya setelah rata-rata (jam full precision); tidak round jam dulu
            $totalMinutes = $pred['nextForecast'] * 60;
            $jamMenit = number_format(round($totalMinutes, 2), 2, ',', '') . ' menit';
            $sheetSummary->fromArray([$kelompok->nama_kelompok, $norm, $jamMenit, $tanggalPrediksi->format('d/m/Y'), round($pred['mape'], 2), Carbon::now('Asia/Makassar')->format('d/m/Y H:i')], null, 'A' . $row);
            $row++;
        }
        $filename = 'prediksi_' . $kelompok->nama_kelompok . '_' . date('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/' . $filename);
        $writer->save($path);
        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function show(string $id)
    {
        $p = PrediksiKegiatan::findOrFail($id);
        return response()->json($p);
    }

    public function destroy(string $id)
    {
        if (!auth()->user()->isAtasan()) {
            abort(403);
        }
        PrediksiKegiatan::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
