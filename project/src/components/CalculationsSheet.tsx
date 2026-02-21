import { useMemo } from 'react';
import { rawData } from '../data/rawData';
import { calculateHoltWinters, HoltWintersResult } from '../utils/holtWinters';

export default function CalculationsSheet() {
  const results = useMemo(() => {
    const dataValues = rawData.map(r => r.durasiJam);
    return calculateHoltWinters(dataValues, { alpha: 0.3, beta: 0.7, gamma: 0.1 }, 73);
  }, []);

  const getStatusBadge = (status: string) => {
    if (status === 'init') {
      return <span className="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700 font-semibold">Init</span>;
    }
    if (status === 'training') {
      return <span className="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 font-semibold">Training</span>;
    }
    return <span className="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700 font-semibold">Testing</span>;
  };

  return (
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-2xl font-bold text-green-800 mb-4">Sheet 3: Perhitungan Holt-Winters Lengkap</h2>

      <div className="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <h3 className="font-semibold text-blue-800 mb-2">Tabel Perhitungan Lengkap</h3>
        <p className="text-sm text-gray-700">
          Berikut adalah hasil perhitungan Holt-Winters untuk setiap record. Setiap baris menunjukkan nilai Level (L<sub>t</sub>),
          Trend (T<sub>t</sub>), Seasonal (S<sub>t</sub>), Forecast (F<sub>t</sub>), dan indikator error.
        </p>
      </div>

      <div className="overflow-x-auto border rounded-lg">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-green-700">
            <tr>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">t</th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">Tanggal</th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">Y<sub>t</sub></th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">L<sub>t</sub></th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">T<sub>t</sub></th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">S<sub>t</sub></th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">F<sub>t</sub></th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">Error</th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">|Error|</th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">APE (%)</th>
              <th className="px-3 py-3 text-left text-xs font-semibold text-white uppercase">Status</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {results.map((result: HoltWintersResult, idx: number) => {
              const bgColor = result.status === 'testing' ? 'bg-orange-50' : (idx % 2 === 0 ? 'bg-white' : 'bg-gray-50');

              return (
                <tr key={result.t} className={bgColor}>
                  <td className="px-3 py-2 text-sm font-mono text-gray-900">{result.t}</td>
                  <td className="px-3 py-2 text-sm text-gray-700">{rawData[idx]?.tanggal || '-'}</td>
                  <td className="px-3 py-2 text-sm font-mono font-semibold text-green-700">{result.Y.toFixed(6)}</td>
                  <td className="px-3 py-2 text-sm font-mono text-gray-700">{result.L.toFixed(6)}</td>
                  <td className="px-3 py-2 text-sm font-mono text-gray-700">{result.T.toFixed(6)}</td>
                  <td className="px-3 py-2 text-sm font-mono text-gray-700">{result.S.toFixed(6)}</td>
                  <td className="px-3 py-2 text-sm font-mono text-gray-700">{result.F.toFixed(6)}</td>
                  <td className="px-3 py-2 text-sm font-mono text-gray-600">
                    {result.error !== null ? (result.error >= 0 ? '+' : '') + result.error.toFixed(6) : '—'}
                  </td>
                  <td className="px-3 py-2 text-sm font-mono text-gray-600">
                    {result.absError !== null ? result.absError.toFixed(6) : '—'}
                  </td>
                  <td className="px-3 py-2 text-sm font-mono font-semibold text-orange-700">
                    {result.ape !== null ? result.ape.toFixed(2) : '—'}
                  </td>
                  <td className="px-3 py-2 text-sm">{getStatusBadge(result.status)}</td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>

      <div className="mt-6 bg-yellow-50 border-l-4 border-yellow-500 p-4">
        <h3 className="font-semibold text-yellow-800 mb-2">Catatan Penting</h3>
        <ul className="text-sm text-gray-700 space-y-1 list-disc list-inside">
          <li>Record pertama (t=1) adalah inisialisasi, tidak memiliki error karena menjadi titik awal model</li>
          <li>Record 2-73 adalah data training yang digunakan untuk melatih model</li>
          <li>Record 74-106 adalah data testing yang digunakan untuk mengevaluasi akurasi model</li>
          <li>APE (Absolute Percentage Error) dihitung untuk setiap record kecuali inisialisasi</li>
        </ul>
      </div>
    </div>
  );
}
