import { useMemo } from 'react';
import { rawData } from '../data/rawData';
import { calculateHoltWinters, calculateMAPE } from '../utils/holtWinters';

export default function ResultsSheet() {
  const results = useMemo(() => {
    const dataValues = rawData.map(r => r.durasiJam);
    return calculateHoltWinters(dataValues, { alpha: 0.3, beta: 0.7, gamma: 0.1 }, 73);
  }, []);

  const testingResults = results.slice(73);
  const mape = calculateMAPE(results, 73);

  const sumAPE = testingResults.reduce((sum, r) => sum + (r.ape || 0), 0);

  const getMapeCategory = (mapeValue: number) => {
    if (mapeValue < 10) return { text: 'Sangat Baik', color: 'green' };
    if (mapeValue < 20) return { text: 'Baik', color: 'blue' };
    if (mapeValue < 50) return { text: 'Cukup Baik', color: 'yellow' };
    return { text: 'Perlu Perbaikan', color: 'orange' };
  };

  const category = getMapeCategory(mape);

  return (
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-2xl font-bold text-green-800 mb-4">Sheet 5: Hasil Evaluasi & MAPE</h2>

      <div className="bg-gradient-to-r from-green-700 to-green-600 text-white rounded-lg p-8 mb-6 text-center">
        <h3 className="text-2xl font-bold mb-3">Nilai MAPE Akhir</h3>
        <div className="text-6xl font-bold mb-3">{mape.toFixed(2)}%</div>
        <div className="bg-white bg-opacity-20 rounded-lg p-4 inline-block">
          <p className="text-lg">Kategori: <strong>{category.text}</strong></p>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div className="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
          <p className="text-sm text-gray-600 mb-1">Total Data</p>
          <p className="text-3xl font-bold text-blue-700">{rawData.length}</p>
          <p className="text-xs text-gray-600 mt-1">Record pekerjaan</p>
        </div>
        <div className="bg-green-50 border-l-4 border-green-500 p-4 rounded">
          <p className="text-sm text-gray-600 mb-1">Data Training</p>
          <p className="text-3xl font-bold text-green-700">73</p>
          <p className="text-xs text-gray-600 mt-1">Record No. 1-73 (80%)</p>
        </div>
        <div className="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
          <p className="text-sm text-gray-600 mb-1">Data Testing</p>
          <p className="text-3xl font-bold text-orange-700">33</p>
          <p className="text-xs text-gray-600 mt-1">Record No. 74-106 (20%)</p>
        </div>
      </div>

      <div className="border rounded-lg p-6 bg-gray-50 mb-6">
        <h3 className="text-xl font-bold text-green-800 mb-4">Cara Perhitungan MAPE</h3>

        <div className="bg-white p-4 rounded border mb-4">
          <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
          <p className="font-mono text-lg bg-green-50 p-3 rounded border border-green-200">
            MAPE = (Σ APE) ÷ Jumlah Data Testing
          </p>
        </div>

        <div className="space-y-3">
          <div className="bg-blue-50 p-4 rounded border border-blue-200">
            <p className="text-sm font-semibold text-blue-800 mb-2">Langkah 1: Hitung Total Σ APE</p>
            <p className="text-sm text-gray-700">Jumlahkan semua APE dari data testing (Record 74-106):</p>
            <p className="font-mono text-sm mt-2 bg-white p-2 rounded">
              Σ APE = {testingResults.slice(0, 3).map(r => r.ape?.toFixed(2)).join(' + ')} + ... (33 data testing)
            </p>
            <p className="font-mono text-lg font-bold text-blue-700 mt-2">
              Σ APE = {sumAPE.toFixed(2)}%
            </p>
          </div>

          <div className="bg-green-50 p-4 rounded border border-green-200">
            <p className="text-sm font-semibold text-green-800 mb-2">Langkah 2: Jumlah Data Testing</p>
            <p className="font-mono text-lg font-bold text-green-700">
              n = {testingResults.length} record
            </p>
          </div>

          <div className="bg-orange-50 p-4 rounded border border-orange-200">
            <p className="text-sm font-semibold text-orange-800 mb-2">Langkah 3: Hitung MAPE</p>
            <p className="font-mono text-sm">MAPE = {sumAPE.toFixed(2)} ÷ {testingResults.length}</p>
            <p className="font-mono text-2xl font-bold text-orange-700 mt-2">
              MAPE = {mape.toFixed(2)}%
            </p>
          </div>
        </div>
      </div>

      <div className="border rounded-lg p-6 bg-yellow-50 border-yellow-300 mb-6">
        <h3 className="text-xl font-bold text-yellow-800 mb-4">Kategori Akurasi MAPE</h3>
        <div className="space-y-2">
          <div className={`flex items-center justify-between p-3 rounded ${mape < 10 ? 'bg-green-100 border-2 border-green-500' : 'bg-white'}`}>
            <span className="font-semibold">Sangat Baik</span>
            <span className="font-mono">&lt; 10%</span>
          </div>
          <div className={`flex items-center justify-between p-3 rounded ${mape >= 10 && mape < 20 ? 'bg-blue-100 border-2 border-blue-500' : 'bg-white'}`}>
            <span className="font-semibold">Baik</span>
            <span className="font-mono">10% – 20%</span>
          </div>
          <div className={`flex items-center justify-between p-3 rounded ${mape >= 20 && mape < 50 ? 'bg-yellow-100 border-2 border-yellow-500' : 'bg-white'}`}>
            <span className="font-semibold">Cukup Baik</span>
            <span className="font-mono">20% – 50%</span>
          </div>
          <div className={`flex items-center justify-between p-3 rounded ${mape >= 50 ? 'bg-orange-100 border-2 border-orange-500' : 'bg-white'}`}>
            <span className="font-semibold">Perlu Perbaikan</span>
            <span className="font-mono">&gt; 50%</span>
          </div>
        </div>
      </div>

      <div className="bg-white border-2 border-green-200 rounded-lg p-6">
        <h3 className="text-xl font-bold text-green-800 mb-4">Interpretasi Hasil</h3>
        <div className="space-y-3 text-sm text-gray-700">
          <p>
            Nilai MAPE sebesar <strong className="text-orange-600">{mape.toFixed(2)}%</strong> menunjukkan bahwa
            rata-rata kesalahan prediksi model Holt-Winters per-record adalah sekitar {mape.toFixed(2)}% dari nilai aktual.
          </p>
          <p>
            Model ini dievaluasi menggunakan 33 record testing (No. 74-106) yang merupakan 20% dari total data.
            Data training (73 record pertama) digunakan untuk melatih model dan menentukan pola level, trend, dan seasonal.
          </p>
          {mape > 50 && (
            <div className="bg-orange-50 border-l-4 border-orange-500 p-3 rounded mt-3">
              <p className="font-semibold text-orange-800 mb-1">Catatan Penting:</p>
              <p>
                Tingginya nilai MAPE dalam pendekatan per-record ini disebabkan oleh <strong>variabilitas durasi pekerjaan
                yang sangat tinggi antar-record</strong>. Durasi bisa berkisar dari beberapa menit hingga hampir satu jam
                dalam satu hari yang sama, sehingga pola sulit diprediksi secara akurat per-transaksi.
              </p>
              <p className="mt-2">
                Pendekatan per-record lebih cocok jika ada pola yang konsisten antar-transaksi. Jika variabilitas terlalu tinggi,
                pendekatan per-bulan atau agregasi lainnya cenderung memberikan akurasi yang lebih baik.
              </p>
            </div>
          )}
        </div>
      </div>

      <div className="mt-6 bg-gradient-to-r from-gray-700 to-gray-600 text-white rounded-lg p-6">
        <h3 className="text-lg font-bold mb-3">Kesimpulan</h3>
        <p className="text-sm leading-relaxed">
          Model Triple Exponential Smoothing (Holt-Winters) telah berhasil diimplementasikan untuk peramalan durasi
          perbaikan meteran per-record di PLN Galesong. Dengan parameter α=0.3, β=0.7, dan γ=0.1, model menghasilkan
          MAPE sebesar {mape.toFixed(2)}% pada data testing. Model ini memberikan gambaran tentang pola durasi per-transaksi
          yang dapat dikembangkan lebih lanjut untuk meningkatkan akurasi prediksi.
        </p>
      </div>
    </div>
  );
}
