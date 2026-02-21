import { rawData } from '../data/rawData';

export default function DataSheet() {
  return (
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-2xl font-bold text-green-800 mb-4">Sheet 1: Data Mentah</h2>
      <div className="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <p className="text-sm text-gray-700">
          <strong>Catatan:</strong> Berikut adalah seluruh data mentah perbaikan meteran PLN Galesong tahun 2025.
          Setiap baris merupakan satu transaksi pekerjaan yang akan menjadi nilai Y<sub>t</sub> dalam perhitungan Holt-Winters.
        </p>
        <p className="text-sm text-gray-700 mt-2">
          Total: <strong>{rawData.length} record</strong> | Training: <strong>73 record</strong> (No. 1-73) | Testing: <strong>33 record</strong> (No. 74-106)
        </p>
      </div>

      <div className="overflow-x-auto border rounded-lg">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-green-700">
            <tr>
              <th className="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
              <th className="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Hari</th>
              <th className="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal</th>
              <th className="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Waktu Mulai</th>
              <th className="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Waktu Selesai</th>
              <th className="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Durasi (H:MM:SS)</th>
              <th className="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Durasi (Jam)</th>
              <th className="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Bulan</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {rawData.map((row, idx) => (
              <tr key={row.no} className={idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                <td className="px-4 py-2 text-sm font-mono text-gray-900">{row.no}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{row.hari}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{row.tanggal}</td>
                <td className="px-4 py-2 text-sm font-mono text-gray-700">{row.waktuMulai}</td>
                <td className="px-4 py-2 text-sm font-mono text-gray-700">{row.waktuSelesai}</td>
                <td className="px-4 py-2 text-sm font-mono text-gray-700">{row.durasiHMS}</td>
                <td className="px-4 py-2 text-sm font-mono font-semibold text-green-700">{row.durasiJam.toFixed(6)}</td>
                <td className="px-4 py-2 text-sm text-gray-600">{row.bulan}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="mt-6 bg-yellow-50 border-l-4 border-yellow-500 p-4">
        <h3 className="font-semibold text-yellow-800 mb-2">Cara Konversi Durasi ke Jam Desimal</h3>
        <p className="text-sm text-gray-700">
          <strong>Rumus:</strong> Jam Desimal = Jam + (Menit ÷ 60) + (Detik ÷ 3600)
        </p>
        <p className="text-sm text-gray-700 mt-2">
          <strong>Contoh Record No. 1 (0:06:17):</strong><br/>
          = 0 + (6 ÷ 60) + (17 ÷ 3600)<br/>
          = 0 + 0.1000 + 0.00472<br/>
          = <strong>0.104722 jam</strong>
        </p>
      </div>
    </div>
  );
}
