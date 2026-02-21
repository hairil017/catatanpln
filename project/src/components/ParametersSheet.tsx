import { rawData } from '../data/rawData';

export default function ParametersSheet() {
  const first5 = rawData.slice(0, 5);
  const sumFirst5 = first5.reduce((sum, r) => sum + r.durasiJam, 0);
  const L0 = sumFirst5 / 5;

  const trainingData = rawData.slice(0, 73);
  const last5Training = trainingData.slice(-5);
  const avgFirst5 = sumFirst5 / 5;
  const avgLast5 = last5Training.reduce((sum, r) => sum + r.durasiJam, 0) / 5;
  const T0 = (avgLast5 - avgFirst5) / 68;

  const Y1 = rawData[0].durasiJam;
  const S0 = Y1 - (L0 + T0);
  const F1 = L0 + T0 + S0;

  return (
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-2xl font-bold text-green-800 mb-4">Sheet 2: Parameter & Inisialisasi</h2>

      <div className="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6">
        <h3 className="font-semibold text-orange-800 mb-2">Parameter Model Holt-Winters</h3>
        <div className="grid grid-cols-3 gap-4">
          <div className="bg-white p-3 rounded border">
            <p className="text-xs text-gray-600">Alpha (α) - Level</p>
            <p className="text-2xl font-bold text-green-700">0.3</p>
          </div>
          <div className="bg-white p-3 rounded border">
            <p className="text-xs text-gray-600">Beta (β) - Trend</p>
            <p className="text-2xl font-bold text-green-700">0.7</p>
          </div>
          <div className="bg-white p-3 rounded border">
            <p className="text-xs text-gray-600">Gamma (γ) - Seasonal</p>
            <p className="text-2xl font-bold text-green-700">0.1</p>
          </div>
        </div>
      </div>

      <div className="space-y-6">
        <div className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-green-800 mb-4">1. Perhitungan Level Awal (L₀)</h3>
          <div className="bg-white p-4 rounded border mb-4">
            <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
            <p className="font-mono text-sm bg-gray-100 p-2 rounded">L₀ = (Y₁ + Y₂ + Y₃ + Y₄ + Y₅) ÷ 5</p>
          </div>

          <div className="space-y-2">
            <p className="text-sm"><strong>Data 5 record pertama:</strong></p>
            {first5.map((r, idx) => (
              <p key={idx} className="text-sm font-mono pl-4">
                Y<sub>{idx + 1}</sub> = {r.durasiJam.toFixed(6)} jam
              </p>
            ))}

            <div className="mt-4 bg-green-50 p-3 rounded border border-green-200">
              <p className="text-sm font-mono">
                L₀ = ({first5.map(r => r.durasiJam.toFixed(6)).join(' + ')}) ÷ 5
              </p>
              <p className="text-sm font-mono">L₀ = {sumFirst5.toFixed(6)} ÷ 5</p>
              <p className="text-lg font-bold font-mono text-green-700 mt-2">
                L₀ = {L0.toFixed(6)}
              </p>
            </div>
          </div>
        </div>

        <div className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-green-800 mb-4">2. Perhitungan Trend Awal (T₀)</h3>
          <div className="bg-white p-4 rounded border mb-4">
            <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
            <p className="font-mono text-sm bg-gray-100 p-2 rounded">T₀ = (Ȳ_akhir − Ȳ_awal) ÷ n</p>
          </div>

          <div className="space-y-2">
            <p className="text-sm"><strong>Rata-rata 5 data awal training (No. 1-5):</strong></p>
            <p className="text-sm font-mono pl-4">Ȳ_awal = {avgFirst5.toFixed(6)}</p>

            <p className="text-sm mt-3"><strong>Rata-rata 5 data akhir training (No. 69-73):</strong></p>
            {last5Training.map((r, idx) => (
              <p key={idx} className="text-sm font-mono pl-4">
                Y<sub>{69 + idx}</sub> = {r.durasiJam.toFixed(6)} jam
              </p>
            ))}
            <p className="text-sm font-mono pl-4">Ȳ_akhir = {avgLast5.toFixed(6)}</p>

            <div className="mt-4 bg-green-50 p-3 rounded border border-green-200">
              <p className="text-sm font-mono">T₀ = ({avgLast5.toFixed(6)} − {avgFirst5.toFixed(6)}) ÷ 68</p>
              <p className="text-sm font-mono">T₀ = {(avgLast5 - avgFirst5).toFixed(6)} ÷ 68</p>
              <p className="text-lg font-bold font-mono text-green-700 mt-2">
                T₀ = {T0.toFixed(6)}
              </p>
              <p className="text-xs text-gray-600 mt-2">Tren positif kecil - ada kenaikan durasi per-record yang gradual</p>
            </div>
          </div>
        </div>

        <div className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-green-800 mb-4">3. Perhitungan Seasonal Awal (S₀)</h3>
          <div className="bg-white p-4 rounded border mb-4">
            <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
            <p className="font-mono text-sm bg-gray-100 p-2 rounded">S₀ = Y₁ − (L₀ + T₀)</p>
          </div>

          <div className="space-y-2">
            <p className="text-sm"><strong>Data Aktual Pertama (Y₁):</strong></p>
            <p className="text-sm font-mono pl-4">Y₁ = {Y1.toFixed(6)} jam</p>

            <div className="mt-4 bg-green-50 p-3 rounded border border-green-200">
              <p className="text-sm font-mono">Baseline = L₀ + T₀ = {L0.toFixed(6)} + {T0.toFixed(6)} = {(L0 + T0).toFixed(6)}</p>
              <p className="text-sm font-mono mt-2">S₀ = {Y1.toFixed(6)} − {(L0 + T0).toFixed(6)}</p>
              <p className="text-lg font-bold font-mono text-green-700 mt-2">
                S₀ = {S0.toFixed(6)}
              </p>
              <p className="text-xs text-gray-600 mt-2">Nilai negatif menunjukkan record pertama memiliki durasi di bawah rata-rata baseline</p>
            </div>
          </div>
        </div>

        <div className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-green-800 mb-4">4. Forecast Awal (F₁)</h3>
          <div className="bg-white p-4 rounded border mb-4">
            <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
            <p className="font-mono text-sm bg-gray-100 p-2 rounded">F₁ = L₀ + T₀ + S₀</p>
          </div>

          <div className="bg-green-50 p-3 rounded border border-green-200">
            <p className="text-sm font-mono">F₁ = {L0.toFixed(6)} + {T0.toFixed(6)} + ({S0.toFixed(6)})</p>
            <p className="text-lg font-bold font-mono text-green-700 mt-2">
              F₁ = {F1.toFixed(6)}
            </p>
            <p className="text-xs text-gray-600 mt-2">Forecast pertama sama dengan Y₁ (titik awal model)</p>
          </div>
        </div>

        <div className="bg-gradient-to-r from-green-700 to-green-600 text-white rounded-lg p-6">
          <h3 className="text-xl font-bold mb-4">Ringkasan Nilai Inisialisasi</h3>
          <div className="grid grid-cols-2 gap-4">
            <div className="bg-white bg-opacity-20 p-4 rounded">
              <p className="text-sm opacity-90">Y₁ (Data Aktual)</p>
              <p className="text-2xl font-bold font-mono">{Y1.toFixed(6)}</p>
            </div>
            <div className="bg-white bg-opacity-20 p-4 rounded">
              <p className="text-sm opacity-90">L₀ (Level Awal)</p>
              <p className="text-2xl font-bold font-mono">{L0.toFixed(6)}</p>
            </div>
            <div className="bg-white bg-opacity-20 p-4 rounded">
              <p className="text-sm opacity-90">T₀ (Trend Awal)</p>
              <p className="text-2xl font-bold font-mono">{T0.toFixed(6)}</p>
            </div>
            <div className="bg-white bg-opacity-20 p-4 rounded">
              <p className="text-sm opacity-90">S₀ (Seasonal Awal)</p>
              <p className="text-2xl font-bold font-mono">{S0.toFixed(6)}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
