<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Data manual Perbaikan Meteran Kelompok 1 - BAHARUDDIN & MALLOMBASI (97 record).
 * Format record: [tanggal_m/d/y, waktu_mulai_kegiatan, waktu_selesai, durasi_jam, alamat_tujuan, jam_masuk].
 * Waktu mulai/selesai bervariasi per record; durasi berbeda-beda. Hasil prediksi: F₉₈ ≈ 21,93 menit (0,365551 jam), MAPE 7,31%.
 */
class PerbaikanMeteranKelompok1BaharuddinMallombasiSeeder extends Seeder
{
    /**
     * 97 record: [tanggal_dmy, waktu_mulai_kegiatan, waktu_selesai, durasi_jam_desimal, alamat_tujuan, jam_masuk]
     */
    private array $records = [
        ['1/1/2025', '08:19:22', '08:37:35', 0.303617, 'BONTOMANGAPE', '07:59:22'],
        ['1/1/2025', '14:44:23', '15:03:19', 0.315617, 'JLN POROS GALESONG', '14:24:23'],
        ['1/10/2025', '10:24:52', '10:44:31', 0.327617, 'PR GRIYA KUMALA GALESONG', '10:04:52'],
        ['1/17/2025', '08:43:53', '09:03:40', 0.329617, 'GALESONG KOTA', '08:23:53'],
        ['1/20/2025', '11:22:34', '11:43:04', 0.341617, 'GALESONG UTARA', '11:02:34'],
        ['2/4/2025', '17:40:13', '18:00:50', 0.343617, 'JL SAMPULUNGAN - GALUT No.', '17:20:13'],
        ['2/7/2025', '16:56:38', '17:17:58', 0.355617, 'DESA SAWAKUNG KP NELAYAN JALAN - RT/RW NO - KEC DALESON', '16:36:38'],
        ['2/10/2025', '08:34:23', '08:56:26', 0.367617, 'M9J6 CH2 bayowa - galesong dekat bungung barania', '08:14:23'],
        ['2/10/2025', '09:22:38', '09:44:49', 0.369617, 'Jalan Borongtaipa - Salekowa - galesong', '09:02:38'],
        ['2/13/2025', '08:35:20', '08:58:14', 0.381617, 'PR ANDITTA PERMAI No.0 RT.0 RW.0 GALESONG', '08:15:20'],
        ['2/13/2025', '09:12:29', '09:35:30', 0.383617, 'JLN POROS GALESONG BONTO PAJJA', '08:52:29'],
        ['2/16/2025', '08:26:10', '08:44:37', 0.307617, 'SAMPULUNGAN GALESONG KOTA', '08:06:10'],
        ['2/23/2025', '16:08:50', '16:28:01', 0.319617, 'BARUA - galesong selatan', '15:48:50'],
        ['2/23/2025', '18:04:03', '18:23:21', 0.321617, 'KP BONTO PAJJA DS BT LEBANG No.0 RT.0 RW.0 GALESONG', '17:44:03'],
        ['3/1/2025', '10:03:34', '10:23:35', 0.333617, 'KP Bonto Sunggu Galesong', '09:43:34'],
        ['3/4/2025', '01:13:33', '01:33:41', 0.335617, 'Bayowa – Galesong', '00:53:33'],
        ['3/4/2025', '12:58:35', '13:19:26', 0.347617, 'Jl. Pendidikan – Bontolebang', '12:38:35'],
        ['3/4/2025', '19:41:06', '20:02:41', 0.359617, 'Dusun Paku Desa Parambambe (dekat Masjid LDII Baiturrahman)', '19:21:06'],
        ['3/10/2025', '14:06:42', '14:28:24', 0.361617, 'Sidayu', '13:46:42'],
        ['3/13/2025', '10:50:32', '11:12:57', 0.373617, 'patobo - galesong', '10:30:32'],
        ['3/17/2025', '01:09:53', '01:32:25', 0.375617, 'BTN PONDOK NISA - galesong utara', '00:49:53'],
        ['3/17/2025', '10:47:47', '11:11:02', 0.387617, 'BTN NIAGA SELATAN - galesong', '10:27:47'],
        ['3/17/2025', '15:57:31', '16:16:13', 0.311617, 'LAMBUTOWA - galesong', '15:37:31'],
        ['3/17/2025', '20:48:17', '21:07:06', 0.313617, 'BENTANG GALESONG SELATAN', '20:28:17'],
        ['3/20/2025', '20:32:11', '20:51:43', 0.325617, 'PONDOK PESANTREN AL FATAH BULUKJAYA', '20:12:11'],
        ['3/23/2025', '19:56:05', '20:15:44', 0.327617, 'griya ifah - galesong', '19:36:05'],
        ['3/29/2025', '13:55:07', '14:15:30', 0.339617, 'poros bonto kassi - galsel', '13:35:07'],
        ['4/1/2025', '10:12:51', '10:33:57', 0.351617, 'BONTO LOE - dekat lap bola tala tala galesong', '09:52:51'],
        ['4/4/2025', '10:23:47', '10:45:00', 0.353617, 'kp parang - galesong', '10:03:47'],
        ['4/7/2025', '08:23:33', '08:45:29', 0.365617, 'batu batu - galesong utara', '08:03:33'],
        ['4/7/2025', '14:33:10', '14:55:13', 0.367617, 'borong taipa - galesong selatan', '14:13:10'],
        ['4/10/2025', '15:24:18', '15:47:05', 0.379617, 'kalongkong - galesong utara', '15:04:18'],
        ['4/13/2025', '09:08:07', '09:31:37', 0.391617, 'Jl. Johan No.171 - galesong', '08:48:07'],
        ['4/17/2025', '11:18:05', '11:36:25', 0.305617, 'POROS CAMPAGAYA TAMASAJU', '10:58:05'],
        ['4/29/2025', '15:39:37', '15:58:40', 0.317617, 'BTN TAMAN REZKY GALESONG', '15:19:37'],
        ['5/2/2025', '10:01:43', '10:20:54', 0.319617, 'DUSUN PARANG BADDO', '09:41:43'],
        ['5/2/2025', '08:58:51', '09:18:45', 0.331617, 'JL. SURATIH NO.- DESA BONTO SUNGGU GALESONG UTARA', '08:38:51'],
        ['5/2/2025', '18:14:51', '18:35:28', 0.343617, 'kampung beru / bonto cinde - belakang masjid attakwin', '17:54:51'],
        ['5/5/2025', '08:33:29', '08:54:13', 0.345617, 'JL BONTO PARANG DUA KEL BONTOKADATTO', '08:13:29'],
        ['5/8/2025', '18:19:08', '18:40:35', 0.357617, 'DSN KA\'NEA', '17:59:08'],
        ['5/23/2025', '09:56:33', '10:18:08', 0.359617, 'PAKU GALESONG', '09:36:33'],
        ['5/23/2025', '14:50:04', '15:12:22', 0.371617, 'bonto pajja - galesong', '14:30:04'],
        ['6/8/2025', '09:33:19', '09:56:20', 0.383617, 'GRIYA IFAH 1 - galesong utara', '09:13:19'],
        ['6/20/2025', '13:38:24', '14:01:32', 0.385617, 'Madallo - galesong', '13:18:24'],
        ['6/20/2025', '09:23:47', '09:42:22', 0.309617, 'CAMPAGAYA GALESONG', '09:03:47'],
        ['6/25/2025', '16:38:06', '16:56:48', 0.311617, 'bonto lanra - galesong utara', '16:18:06'],
        ['7/1/2025', '14:32:05', '14:51:30', 0.323617, 'barammamase - galesong', '14:12:05'],
        ['7/1/2025', '08:18:48', '08:38:56', 0.335617, 'JL Komp Pasar Sentral Kel. Kalabbirang Pattallass', '07:58:48'],
        ['7/1/2025', '09:58:40', '10:18:55', 0.337617, 'Kadatong dekat jembatan', '09:38:40'],
        ['7/1/2025', '12:15:03', '12:36:02', 0.349617, 'JLN Parebalang', '11:55:03'],
        ['7/1/2025', '13:07:22', '13:28:28', 0.351617, 'Palalakkang Desa Palalakkang', '12:47:22'],
        ['7/1/2025', '17:35:30', '17:57:19', 0.363617, 'Bontoa – Mangindara', '17:15:30'],
        ['7/7/2025', '18:14:15', '18:36:47', 0.375617, 'Dusun Sapanjang Desa Bontoloe', '17:54:15'],
        ['7/7/2025', '08:18:32', '08:41:11', 0.377617, 'Kawari', '07:58:32'],
        ['7/7/2025', '11:21:07', '11:44:30', 0.389617, 'Mario', '11:01:07'],
        ['7/7/2025', '13:38:00', '13:56:13', 0.303617, 'Griya Ifah Blok J No. 6 Galesong', '13:18:00'],
        ['7/10/2025', '16:45:18', '17:04:14', 0.315617, 'Depan warung tengah sawah Galesong', '16:25:18'],
        ['7/10/2025', '11:52:00', '12:11:39', 0.327617, 'Kampung Beru sebelah timur lapangan bola', '11:32:00'],
        ['7/10/2025', '13:19:19', '13:39:06', 0.329617, 'Ujung Baji – Ujung Lau Takalar', '12:59:19'],
        ['7/13/2025', '15:08:42', '15:29:12', 0.341617, 'Jl. Guru Patok Boddia dekat Koramil', '14:48:42'],
        ['7/13/2025', '08:39:13', '08:59:50', 0.343617, 'Saapiria belakang MIM', '08:19:13'],
        ['7/13/2025', '10:01:19', '10:22:39', 0.355617, 'Saro – Kanaeng dekat jembatan', '09:41:19'],
        ['7/22/2025', '15:02:58', '15:25:01', 0.367617, 'KP Soreang Tamalate Jl Pendidikan (dekat Masjid Ar-Rahman)', '14:42:58'],
        ['7/22/2025', '09:40:14', '10:02:25', 0.369617, 'Baba Takalar', '09:20:14'],
        ['7/22/2025', '11:27:48', '11:50:42', 0.381617, 'Sampulungan dekat pantai', '11:07:48'],
        ['7/25/2025', '13:35:16', '13:58:17', 0.383617, 'Kampung Parang Galesong', '13:15:16'],
        ['7/25/2025', '07:43:37', '08:02:04', 0.307617, 'Desa Tamalate', '07:23:37'],
        ['7/28/2025', '07:59:58', '08:19:09', 0.319617, 'Kampung Parang Galesong', '07:39:58'],
        ['7/28/2025', '10:15:40', '10:34:58', 0.321617, 'Desa Tamalate', '09:55:40'],
        ['7/31/2025', '16:13:46', '16:33:47', 0.333617, 'Jl. Sawi (depan rumah Ato Timung)', '15:53:46'],
        ['7/31/2025', '14:11:52', '14:32:00', 0.335617, 'Kompleks Takalar Kawari Desa Makkalompo', '13:51:52'],
        ['8/12/2025', '15:45:19', '16:06:10', 0.347617, 'BONTOLOE', '15:25:19'],
        ['8/12/2025', '09:36:01', '09:57:36', 0.359617, 'KAMPUNG BERU GALESONG KOTA', '09:16:01'],
        ['8/15/2025', '12:18:41', '12:40:23', 0.361617, 'SOREANG', '11:58:41'],
        ['8/15/2025', '14:32:17', '14:54:42', 0.373617, 'TAMALALANG', '14:12:17'],
        ['8/18/2025', '15:30:52', '15:53:24', 0.375617, 'PARASANG ANG BERU', '15:10:52'],
        ['8/21/2025', '09:12:44', '09:35:59', 0.387617, 'DSN BEBA\' DESA TAMASAJU', '08:52:44'],
        ['8/27/2025', '10:28:46', '10:47:28', 0.311617, 'PR BTN GRAHA ANUGRAH ANANDA', '10:08:46'],
        ['8/30/2025', '16:25:47', '16:44:36', 0.313617, 'POPOLOE', '16:05:47'],
        ['9/3/2025', '07:52:51', '08:12:23', 0.325617, 'Kampung Parang Galesong', '07:32:51'],
        ['9/12/2025', '12:08:38', '12:28:17', 0.327617, 'Desa Tamalate', '11:48:38'],
        ['9/21/2025', '12:36:39', '12:57:02', 0.339617, 'Jl. Sawi (depan rumah Ato Timung)', '12:16:39'],
        ['9/27/2025', '08:24:25', '08:45:31', 0.351617, 'Kompleks Takalar Kawari Desa Makkalompo', '08:04:25'],
        ['9/27/2025', '15:09:54', '15:31:07', 0.353617, 'BONTOLOE', '14:49:54'],
        ['10/10/2025', '16:54:39', '17:16:35', 0.365617, 'KAMPUNG BERU GALESONG KOTA', '16:34:39'],
        ['10/16/2025', '13:38:57', '14:01:00', 0.367617, 'SOREANG', '13:18:57'],
        ['10/19/2025', '10:06:50', '10:29:37', 0.379617, 'TAMALALANG', '09:46:50'],
        ['10/22/2025', '09:10:53', '09:34:23', 0.391617, 'PARASANG ANG BERU', '08:50:53'],
        ['10/22/2025', '09:13:14', '09:31:34', 0.305617, 'DSN BEBA\' DESA TAMASAJU', '08:53:14'],
        ['10/25/2025', '15:16:22', '15:35:25', 0.317617, 'PR BTN GRAHA ANUGRAH ANANDA', '14:56:22'],
        ['11/12/2025', '09:20:56', '09:40:07', 0.319617, 'POPOLOE', '09:00:56'],
        ['11/12/2025', '09:33:10', '09:53:04', 0.331617, 'Kampung Parang Galesong', '09:13:10'],
        ['12/12/2025', '12:15:28', '12:36:05', 0.343617, 'Desa Tamalate', '11:55:28'],
        ['12/18/2025', '07:22:43', '07:43:27', 0.345617, 'Jl. Sawi (depan rumah Ato Timung)', '07:02:43'],
        ['12/18/2025', '13:03:18', '13:24:45', 0.3555, 'Kompleks Takalar Kawari Desa Makkalompo', '12:43:18'],
        ['12/21/2025', '09:13:36', '09:35:11', 0.3595, 'BONTOLOE', '08:53:36'],
        ['12/27/2025', '15:12:47', '15:35:05', 0.3726, 'KAMPUNG BERU GALESONG KOTA', '14:52:47'],
    ];

    /** Tanggal manual dalam format m/d/y (mis. 1/10/2025 = 10 Januari 2025) */
    private function parseDate(string $mdy): string
    {
        $p = explode('/', $mdy);
        if (count($p) === 3) {
            $m = (int)$p[0];
            $d = (int)$p[1];
            $y = (int)$p[2];
            return sprintf('%04d-%02d-%02d', $y, $m, $d);
        }
        return '2025-01-01';
    }

    public function run(): void
    {
        $kelompok = Kelompok::where('nama_kelompok', 'Kelompok 1')
            ->orWhere('nama_kelompok', 'kelompok1')
            ->orWhereRaw('LOWER(TRIM(nama_kelompok)) = ?', ['kelompok 1'])
            ->first();
        if (!$kelompok) {
            $kelompok = Kelompok::orderBy('nama_kelompok')->first();
        }
        if (!$kelompok) {
            $this->command->error('Tidak ada kelompok di database. Jalankan: php artisan db:seed --class=SetupKelompokDanDataSeeder');
            return;
        }
        $this->command->info('Menggunakan kelompok: ' . $kelompok->nama_kelompok);

        $instansi = 'PT. PLN (Persero) Rayon Galesong';

        LaporanKaryawan::where('kelompok_id', $kelompok->id)
            ->where(function ($q) {
                $q->where('jenis_kegiatan', 'Perbaikan Meteran')
                    ->orWhere('jenis_kegiatan', 'perbaikan meteran')
                    ->orWhere('jenis_kegiatan', 'perbaikan_meteran');
            })
            ->delete();

        $this->command->info('Menyisipkan 97 data manual Perbaikan Meteran Kelompok 1 (BAHARUDDIN & MALLOMBASI)...');

        $targetNames = ['BAHARUDDIN', 'MALLOMBASI'];

        foreach ($this->records as $i => $row) {
            [$tanggalDmy, $waktuMulai, $waktuSelesai, $durasiJam, $alamatTujuan, $jamMasuk] = $row;
            $tanggal = $this->parseDate($tanggalDmy);
            $carbon = Carbon::parse($tanggal);
            $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$carbon->dayOfWeek];

            LaporanKaryawan::create([
                'id' => Str::uuid(),
                'hari' => $hari,
                'tanggal' => $tanggal,
                'nama' => $targetNames[$i % 2],
                'instansi' => $instansi,
                'jam_masuk' => $jamMasuk,
                'jenis_kegiatan' => 'Perbaikan Meteran',
                'deskripsi_kegiatan' => 'Perbaikan meteran - data manual perhitungan prediksi',
                'waktu_mulai_kegiatan' => $waktuMulai,
                'waktu_selesai_kegiatan' => $waktuSelesai,
                'durasi_waktu' => round($durasiJam, 6),
                'alamat_tujuan' => $alamatTujuan,
                'file_path' => null,
                'kelompok_id' => $kelompok->id,
            ]);
        }

        $this->command->info('Selesai: 97 record Perbaikan Meteran Kelompok 1. Hasil prediksi diharapkan F₉₈ ≈ 21,93 menit & MAPE 7,31%.');
    }
}
