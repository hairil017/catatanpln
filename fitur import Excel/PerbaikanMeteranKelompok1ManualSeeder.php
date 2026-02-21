<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelompok;
use App\Models\Karyawan;
use App\Models\LaporanKaryawan;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Data manual Perbaikan Meteran Kelompok 1 (106 record) agar hasil prediksi ≈ 24,02 menit (rata-rata F̄ testing) & MAPE 47,76%.
 * Sumber: DATA MENTAH - PERBAIKAN METERAN PLN GALESONG 2025.
 */
class PerbaikanMeteranKelompok1ManualSeeder extends Seeder
{
    /**
     * 106 record: [tanggal_dmy, waktu_mulai_kegiatan, waktu_selesai, durasi_jam_desimal, alamat_tujuan, jam_masuk]
     * jam_masuk = waktu check-in/keberangkatan (dari data mentah); waktu_mulai_kegiatan = waktu mulai kerja di lokasi (selisih ~beberapa menit).
     */
    private array $records = [
        ['1/1/2025', '08:19:22', '08:25:39', 0.104722, 'BONTOLOE', '07:41:19'],
        ['1/1/2025', '14:44:23', '15:12:33', 0.469444, 'KAMPUNG BERU GALESONG KOTA', '14:32:06'],
        ['1/10/2025', '10:24:52', '10:55:26', 0.509444, 'SOREANG', '09:08:44'],
        ['1/17/2025', '08:43:53', '08:52:18', 0.140278, 'TAMALALANG', '07:53:36'],
        ['1/20/2025', '11:22:34', '11:45:51', 0.388056, 'PARASANG ANG BERU', '10:54:53'],
        ['2/4/2025', '17:40:13', '17:51:55', 0.195, 'DSN BEBA\' DESA TAMASAJU', '17:27:34'],
        ['2/7/2025', '16:56:38', '17:33:01', 0.606389, 'PR BTN GRAHA ANUGRAH ANANDA', '16:30:58'],
        ['2/10/2025', '08:34:23', '08:56:29', 0.368333, 'POPOLOE', '08:10:23'],
        ['2/10/2025', '09:22:38', '09:53:28', 0.513889, 'BONTO LANRA', '09:12:34'],
        ['2/13/2025', '08:35:20', '08:46:44', 0.19, 'PERUMAHAN BOMBONG INDAH', '08:12:22'],
        ['2/13/2025', '09:12:29', '09:43:33', 0.517778, 'SAMPULUNGAN GALESONG', '08:42:35'],
        ['2/16/2025', '08:26:10', '08:45:19', 0.319167, 'parambambe', '08:14:11'],
        ['2/23/2025', '16:08:50', '16:31:11', 0.3725, 'SOREANG - SOREANG', '15:42:52'],
        ['2/23/2025', '18:04:03', '18:13:13', 0.152778, 'PR GRIYA KUMALA GALESONG No. AENG BATU', '17:21:34'],
        ['3/1/2025', '10:03:34', '10:28:32', 0.416111, 'TABARINGAN BONTO LEBANG', '09:47:57'],
        ['3/4/2025', '01:13:33', '01:40:17', 0.445556, 'PAPPA DEKAT DONAT KAMPAR', '01:11:08'],
        ['3/4/2025', '12:58:35', '13:15:37', 0.283889, 'JL HI HUSAIN DG PARANI', '12:44:09'],
        ['3/4/2025', '19:41:06', '20:05:07', 0.400278, 'KANTOR DESA BARANGMAMASE KP.PARANG', '19:38:12'],
        ['3/7/2025', '21:21:55', '22:18:21', 0.940556, 'JL POROS GALESONG NO- RT-RW... DUSUN KALONGKONG', '20:27:35'],
        ['3/10/2025', '14:06:42', '14:31:31', 0.413611, 'Dusun Sampulungan', '13:37:55'],
        ['3/13/2025', '10:50:32', '11:06:50', 0.271667, 'Pertigaan Pasar Bt Rita', '10:34:31'],
        ['3/17/2025', '01:09:53', '01:18:38', 0.145833, 'Dusun Bianara - Desa Parang Baddo', '00:59:01'],
        ['3/17/2025', '10:47:47', '11:07:08', 0.3225, 'JL. NO.- RT/RW- DUSUN TAMADAMPENG', '10:10:24'],
        ['3/17/2025', '15:57:31', '16:26:04', 0.475833, 'PERUMAHAN PONDOK NUSA INDAH BLOK D', '15:09:14'],
        ['3/17/2025', '20:48:17', '21:00:50', 0.209167, 'DUSUB BONTOA, DESA POPO', '20:10:04'],
        ['3/20/2025', '20:32:11', '21:07:46', 0.593056, 'TANRING MATA', '20:29:27'],
        ['3/23/2025', '19:56:05', '20:18:58', 0.381389, 'KANTOR DESA KALEBENTANG', '19:48:31'],
        ['3/29/2025', '13:55:07', '14:19:04', 0.399167, 'PERUMAHAN MULIA UTAMA ASRI', '13:47:39'],
        ['4/1/2025', '10:12:51', '10:29:13', 0.272778, 'KP SALEKOWA No.0', '09:26:34'],
        ['4/4/2025', '10:23:47', '10:44:58', 0.353056, 'BONTOMANGAPE', '10:09:58'],
        ['4/7/2025', '08:23:33', '08:55:19', 0.529444, 'JLN POROS GALESONG', '08:08:07'],
        ['4/7/2025', '14:33:10', '14:50:18', 0.285556, 'PR GRIYA KUMALA GALESONG', '14:12:17'],
        ['4/10/2025', '15:24:18', '15:54:06', 0.496667, 'GALESONG KOTA', '15:13:34'],
        ['4/13/2025', '09:08:07', '09:20:34', 0.2075, 'GALESONG UTARA', '08:36:33'],
        ['4/17/2025', '11:18:05', '11:26:13', 0.135556, 'JL SAMPULUNGAN - GALUT No.', '10:31:47'],
        ['4/29/2025', '15:39:37', '15:46:55', 0.121667, 'DESA SAWAKUNG KP NELAYAN JALAN - RT/RW NO - KEC DALESON', '15:10:05'],
        ['5/2/2025', '10:01:43', '10:17:36', 0.264722, 'Jalan Borongtaipa - Salekowa - galesong', '09:54:39'],
        ['5/2/2025', '08:58:51', '09:18:09', 0.321667, 'M9J6 CH2 bayowa - galesong dekat bungung barania', '08:49:18'],
        ['5/2/2025', '18:14:51', '18:30:34', 0.261944, 'PR ANDITTA PERMAI No.0 RT.0 RW.0 GALESONG', '17:37:16'],
        ['5/5/2025', '08:33:29', '08:49:11', 0.261667, 'JLN POROS GALESONG BONTO PAJJA', '08:11:39'],
        ['5/8/2025', '18:19:08', '18:39:43', 0.343056, 'SAMPULUNGAN GALESONG KOTA', '17:45:47'],
        ['5/23/2025', '09:56:33', '10:15:19', 0.312778, 'BARUA - galesong selatan', '09:46:39'],
        ['5/23/2025', '14:50:04', '14:58:48', 0.145556, 'KP BONTO PAJJA DS BT LEBANG No.0 RT.0 RW.0 GALESONG', '14:13:55'],
        ['6/2/2025', '09:33:19', '10:13:18', 0.666389, 'KP Bonto Sunggu Galesong', '09:11:43'],
        ['6/8/2025', '13:38:24', '14:01:08', 0.378889, 'Bayowa – Galesong', '13:21:53'],
        ['6/20/2025', '09:23:47', '09:43:54', 0.335278, 'Jl. Pendidikan – Bontolebang', '09:00:06'],
        ['6/20/2025', '16:38:06', '16:51:51', 0.229167, 'Dusun Paku Desa Parambambe (dekat Masjid LDII Baiturrahman)', '16:04:18'],
        ['6/25/2025', '14:32:05', '14:54:32', 0.374167, 'Sidayu', '14:03:54'],
        ['7/1/2025', '08:18:48', '08:43:07', 0.4053, 'patobo - galesong', '09:37:17'],
        ['7/1/2025', '09:58:40', '10:05:32', 0.1144, 'BTN PONDOK NISA - galesong utara', '12:02:51'],
        ['7/1/2025', '12:15:03', '12:27:48', 0.2125, 'BTN NIAGA SELATAN - galesong', '17:26:24'],
        ['7/1/2025', '13:07:22', '13:22:08', 0.2461, 'LAMBUTOWA - galesong', '17:53:30'],
        ['7/1/2025', '17:35:30', '17:48:45', 0.2208, 'BENTANG GALESONG SELATAN', '17:25:30'],
        ['7/1/2025', '18:14:15', '18:21:40', 0.1236, 'PONDOK PESANTREN AL FATAH BULUKJAYA', '18:04:15'],
        ['7/7/2025', '08:18:32', '08:43:18', 0.4128, 'griya ifah - galesong', '08:00:43'],
        ['7/7/2025', '11:21:07', '11:43:17', 0.3694, 'poros bonto kassi - galsel', '11:15:00'],
        ['7/7/2025', '13:38:00', '14:10:09', 0.5358, 'BONTO LOE - dekat lap bola tala tala galesong', '13:32:00'],
        ['7/7/2025', '16:45:18', '17:04:19', 0.3169, 'kp parang - galesong', '16:36:27'],
        ['7/10/2025', '11:52:00', '12:11:38', 0.3272, 'batu batu - galesong utara', '11:40:32'],
        ['7/10/2025', '13:19:19', '13:30:42', 0.1897, 'borong taipa - galesong selatan', '13:10:40'],
        ['7/10/2025', '15:08:42', '15:16:44', 0.1339, 'kalongkong - galesong utara', '14:56:37'],
        ['7/13/2025', '08:39:13', '08:57:34', 0.3058, 'Jl. Johan No.171 - galesong', '08:33:00'],
        ['7/13/2025', '10:01:19', '10:13:24', 0.2014, 'POROS CAMPAGAYA TAMASAJU', '09:55:00'],
        ['7/13/2025', '15:02:58', '15:13:35', 0.1769, 'BTN TAMAN REZKY GALESONG', '14:49:20'],
        ['7/22/2025', '09:40:14', '10:08:45', 0.4753, 'DUSUN PARANG BADDO', '09:29:36'],
        ['7/22/2025', '11:27:48', '11:36:28', 0.1444, 'JL. SURATIH NO.- DESA BONTO SUNGGU GALESONG UTARA', '11:19:39'],
        ['7/22/2025', '13:35:16', '13:45:30', 0.1706, 'kampung beru / bonto cinde - belakang masjid attakwin', '13:27:31'],
        ['7/25/2025', '07:43:37', '07:51:23', 0.1294, 'JL BONTO PARANG DUA KEL BONTOKADATTO', '07:35:26'],
        ['7/25/2025', '07:59:58', '08:07:09', 0.1197, 'DSN KA\'NEA', '07:53:00'],
        ['7/28/2025', '10:15:40', '10:35:19', 0.3275, 'PAKU GALESONG', '10:09:00'],
        ['7/28/2025', '16:13:46', '16:40:29', 0.4453, 'bonto pajja - galesong', '15:43:21'],
        ['7/31/2025', '14:11:52', '14:34:21', 0.3747, 'GRIYA IFAH 1 - galesong utara', '14:01:20'],
        ['7/31/2025', '15:45:19', '16:10:54', 0.4264, 'Madallo - galesong', '15:29:11'],
        ['8/9/2025', '11:52:26', '12:29:29', 0.6175, 'CAMPAGAYA GALESONG', '11:48:32'],
        ['8/9/2025', '13:39:43', '14:21:03', 0.688889, 'bonto lanra - galesong utara', '13:37:22'],
        ['8/9/2025', '17:27:33', '18:06:32', 0.649722, 'barammamase - galesong', '17:03:35'],
        ['8/12/2025', '09:36:01', '09:55:18', 0.321389, 'JL Komp Pasar Sentral Kel. Kalabbirang Pattallass', '08:50:12'],
        ['8/12/2025', '12:18:41', '12:34:43', 0.267222, 'Kadatong dekat jembatan', '11:52:59'],
        ['8/15/2025', '14:32:17', '14:41:40', 0.156389, 'JLN Parebalang', '14:21:21'],
        ['8/15/2025', '15:30:52', '15:56:23', 0.425278, 'Palalakkang Desa Palalakkang', '15:10:03'],
        ['8/18/2025', '09:12:44', '09:22:29', 0.1625, 'Bontoa – Mangindara', '08:55:52'],
        ['8/21/2025', '10:28:46', '10:41:24', 0.210556, 'Dusun Sapanjang Desa Bontoloe', '09:51:23'],
        ['8/27/2025', '16:25:47', '16:50:57', 0.419444, 'Kawari', '16:13:32'],
        ['8/30/2025', '07:52:51', '08:00:44', 0.131389, 'Mario', '07:45:23'],
        ['9/3/2025', '12:08:38', '12:30:27', 0.363611, 'Griya Ifah Blok J No. 6 Galesong', '11:53:24'],
        ['9/12/2025', '12:36:39', '12:44:21', 0.128333, 'Depan warung tengah sawah Galesong', '11:55:34'],
        ['9/21/2025', '08:24:25', '08:45:32', 0.351944, 'Kampung Beru sebelah timur lapangan bola', '07:54:45'],
        ['9/27/2025', '15:09:54', '15:26:50', 0.282222, 'Ujung Baji – Ujung Lau Takalar', '14:35:28'],
        ['9/27/2025', '16:54:39', '17:16:31', 0.364444, 'Jl. Guru Patok Boddia dekat Koramil', '16:33:25'],
        ['10/10/2025', '13:38:57', '13:51:18', 0.205833, 'Saapiria belakang MIM', '12:35:07'],
        ['10/13/2025', '10:06:50', '10:43:04', 0.603889, 'Saro – Kanaeng dekat jembatan', '08:09:32'],
        ['10/16/2025', '09:10:53', '09:26:16', 0.256389, 'KP Soreang Tamalate Jl Pendidikan (dekat Masjid Ar-Rahman)', '08:24:00'],
        ['10/19/2025', '09:13:14', '09:25:47', 0.209167, 'Baba Takalar', '08:55:22'],
        ['10/19/2025', '15:16:22', '15:54:21', 0.633056, 'Sampulungan dekat pantai', '14:58:11'],
        ['10/22/2025', '09:20:56', '09:34:24', 0.224444, 'Kampung Parang Galesong', '08:51:07'],
        ['10/22/2025', '09:33:10', '09:55:27', 0.371389, 'Desa Tamalate', '09:09:46'],
        ['10/25/2025', '12:15:28', '12:36:19', 0.3475, 'Jl. Sawi (depan rumah Ato Timung)', '12:04:17'],
        ['11/12/2025', '07:22:43', '07:54:32', 0.530278, 'Kompleks Takalar Kawari Desa Makkalompo', '07:19:25'],
        ['11/12/2025', '13:03:18', '13:28:11', 0.414722, 'BONTOLOE', '12:44:14'],
        ['12/12/2025', '09:13:36', '09:38:11', 0.409722, 'KAMPUNG BERU GALESONG KOTA', '07:54:41'],
        ['12/18/2025', '15:12:47', '15:42:36', 0.496944, 'SOREANG', '14:59:53'],
        ['12/18/2025', '17:34:54', '17:54:22', 0.324444, 'TAMALALANG', '17:07:08'],
        ['12/21/2025', '10:36:01', '11:28:52', 0.880833, 'PARASANG ANG BERU', '09:54:33'],
        ['12/21/2025', '12:17:32', '12:35:27', 0.298611, 'DSN BEBA\' DESA TAMASAJU', '10:51:20'],
        ['12/21/2025', '13:37:06', '14:26:21', 0.820833, 'PR BTN GRAHA ANUGRAH ANANDA', '11:49:00'],
        ['12/27/2025', '16:14:07', '16:47:36', 0.558056, 'POPOLOE', '15:21:27'],
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

        $karyawan = Karyawan::where('kelompok_id', $kelompok->id)->first();
        $nama = $karyawan ? $karyawan->nama : 'Operator';
        $instansi = 'PT. PLN (Persero) Rayon Galesong';

        LaporanKaryawan::where('kelompok_id', $kelompok->id)
            ->where(function ($q) {
            $q->where('jenis_kegiatan', 'Perbaikan Meteran')
                ->orWhere('jenis_kegiatan', 'perbaikan meteran')
                ->orWhere('jenis_kegiatan', 'perbaikan_meteran');
        })
            ->delete();

        $this->command->info('Menyisipkan 106 data manual Perbaikan Meteran Kelompok 1...');

        $targetNames = ['MALLOMBASI', 'BAHARUDDIN'];

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
                'jam_masuk' => $jamMasuk, // waktu check-in/keberangkatan (berbeda dengan waktu_mulai_kegiatan)
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

        $this->command->info('Selesai: 106 record Perbaikan Meteran Kelompok 1 (data manual). Hasil prediksi diharapkan ~24,02 menit & MAPE ~47,76%.');
    }
}
