export default function FormulasSheet() {
  return (
    <div className="bg-white rounded-lg shadow-md p-6">
      <h2 className="text-2xl font-bold text-green-800 mb-4">Sheet 4: Penjelasan Rumus Holt-Winters</h2>

      <div className="space-y-6">
        <div className="bg-gradient-to-r from-green-700 to-green-600 text-white rounded-lg p-6">
          <h3 className="text-xl font-bold mb-3">Parameter Model</h3>
          <div className="space-y-2 text-sm">
            <p><strong>α (Alpha)</strong> = 0.3 → Bobot untuk komponen Level (semakin besar, semakin responsif terhadap perubahan level)</p>
            <p><strong>β (Beta)</strong> = 0.7 → Bobot untuk komponen Trend (semakin besar, semakin cepat menyesuaikan trend)</p>
            <p><strong>γ (Gamma)</strong> = 0.1 → Bobot untuk komponen Seasonal (semakin besar, semakin sensitif terhadap pola musiman)</p>
          </div>
        </div>

        <div className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-green-800 mb-4">1. Forecast (Prediksi) untuk Periode t</h3>
          <div className="bg-white p-4 rounded border">
            <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
            <p className="font-mono text-lg bg-green-50 p-3 rounded border border-green-200">
              F<sub>t</sub> = L<sub>t-1</sub> + T<sub>t-1</sub> + S<sub>t-1</sub>
            </p>
          </div>
          <div className="mt-4 text-sm text-gray-700">
            <p><strong>Keterangan:</strong></p>
            <ul className="list-disc list-inside space-y-1 pl-4">
              <li>F<sub>t</sub> = Forecast (prediksi) untuk periode t</li>
              <li>L<sub>t-1</sub> = Level pada periode sebelumnya (t-1)</li>
              <li>T<sub>t-1</sub> = Trend pada periode sebelumnya (t-1)</li>
              <li>S<sub>t-1</sub> = Seasonal pada periode sebelumnya (t-1)</li>
            </ul>
            <p className="mt-3 bg-blue-50 p-3 rounded border border-blue-200">
              <strong>Contoh Record No. 2:</strong><br/>
              Dari record No. 1 kita punya: L<sub>1</sub> = 0.322389, T<sub>1</sub> = 0.000240, S<sub>1</sub> = -0.217907<br/>
              Maka: F<sub>2</sub> = 0.322389 + 0.000240 + (-0.217907) = <strong>0.104722</strong>
            </p>
          </div>
        </div>

        <div className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-green-800 mb-4">2. Level (L<sub>t</sub>)</h3>
          <div className="bg-white p-4 rounded border">
            <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
            <p className="font-mono text-lg bg-green-50 p-3 rounded border border-green-200">
              L<sub>t</sub> = α(Y<sub>t</sub> − S<sub>t-1</sub>) + (1−α)(L<sub>t-1</sub> + T<sub>t-1</sub>)
            </p>
          </div>
          <div className="mt-4 text-sm text-gray-700">
            <p><strong>Keterangan:</strong></p>
            <ul className="list-disc list-inside space-y-1 pl-4">
              <li>L<sub>t</sub> = Level saat ini (nilai dasar/baseline saat ini)</li>
              <li>Y<sub>t</sub> = Data aktual pada periode t</li>
              <li>α = 0.3 (parameter smoothing untuk level)</li>
              <li>S<sub>t-1</sub> = Seasonal periode sebelumnya</li>
              <li>(L<sub>t-1</sub> + T<sub>t-1</sub>) = Prediksi level periode sebelumnya dengan trend</li>
            </ul>
            <p className="mt-3 bg-blue-50 p-3 rounded border border-blue-200">
              <strong>Contoh Record No. 2:</strong><br/>
              Y<sub>2</sub> = 0.469444, S<sub>1</sub> = -0.217907, L<sub>1</sub> = 0.322389, T<sub>1</sub> = 0.000240<br/>
              L<sub>2</sub> = 0.3 × (0.469444 − (−0.217907)) + 0.7 × (0.322389 + 0.000240)<br/>
              L<sub>2</sub> = 0.3 × 0.687351 + 0.7 × 0.322629<br/>
              L<sub>2</sub> = 0.206205 + 0.225840 = <strong>0.432046</strong>
            </p>
          </div>
        </div>

        <div className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-green-800 mb-4">3. Trend (T<sub>t</sub>)</h3>
          <div className="bg-white p-4 rounded border">
            <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
            <p className="font-mono text-lg bg-green-50 p-3 rounded border border-green-200">
              T<sub>t</sub> = β(L<sub>t</sub> − L<sub>t-1</sub>) + (1−β)T<sub>t-1</sub>
            </p>
          </div>
          <div className="mt-4 text-sm text-gray-700">
            <p><strong>Keterangan:</strong></p>
            <ul className="list-disc list-inside space-y-1 pl-4">
              <li>T<sub>t</sub> = Trend saat ini (kecenderungan naik/turun)</li>
              <li>β = 0.7 (parameter smoothing untuk trend)</li>
              <li>(L<sub>t</sub> − L<sub>t-1</sub>) = Perubahan level dari periode sebelumnya</li>
              <li>T<sub>t-1</sub> = Trend periode sebelumnya</li>
            </ul>
            <p className="mt-3 bg-blue-50 p-3 rounded border border-blue-200">
              <strong>Contoh Record No. 2:</strong><br/>
              L<sub>2</sub> = 0.432046, L<sub>1</sub> = 0.322389, T<sub>1</sub> = 0.000240<br/>
              T<sub>2</sub> = 0.7 × (0.432046 − 0.322389) + 0.3 × 0.000240<br/>
              T<sub>2</sub> = 0.7 × 0.109657 + 0.3 × 0.000240<br/>
              T<sub>2</sub> = 0.076760 + 0.000072 = <strong>0.076832</strong>
            </p>
          </div>
        </div>

        <div className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-green-800 mb-4">4. Seasonal (S<sub>t</sub>)</h3>
          <div className="bg-white p-4 rounded border">
            <p className="text-sm font-semibold text-gray-700 mb-2">Rumus:</p>
            <p className="font-mono text-lg bg-green-50 p-3 rounded border border-green-200">
              S<sub>t</sub> = γ(Y<sub>t</sub> − L<sub>t</sub>) + (1−γ)S<sub>t-1</sub>
            </p>
          </div>
          <div className="mt-4 text-sm text-gray-700">
            <p><strong>Keterangan:</strong></p>
            <ul className="list-disc list-inside space-y-1 pl-4">
              <li>S<sub>t</sub> = Seasonal saat ini (pola musiman/fluktuasi)</li>
              <li>γ = 0.1 (parameter smoothing untuk seasonal)</li>
              <li>(Y<sub>t</sub> − L<sub>t</sub>) = Deviasi data aktual dari level saat ini</li>
              <li>S<sub>t-1</sub> = Seasonal periode sebelumnya</li>
            </ul>
            <p className="mt-3 bg-blue-50 p-3 rounded border border-blue-200">
              <strong>Contoh Record No. 2:</strong><br/>
              Y<sub>2</sub> = 0.469444, L<sub>2</sub> = 0.432046, S<sub>1</sub> = -0.217907<br/>
              S<sub>2</sub> = 0.1 × (0.469444 − 0.432046) + 0.9 × (−0.217907)<br/>
              S<sub>2</sub> = 0.1 × 0.037398 + 0.9 × (−0.217907)<br/>
              S<sub>2</sub> = 0.003740 + (−0.196116) = <strong>−0.192376</strong>
            </p>
          </div>
        </div>

        <div className="border rounded-lg p-6 bg-orange-50 border-orange-300">
          <h3 className="text-xl font-bold text-orange-800 mb-4">5. Perhitungan Error & APE</h3>

          <div className="space-y-4">
            <div>
              <p className="text-sm font-semibold text-gray-700 mb-2">Error (Selisih):</p>
              <p className="font-mono text-lg bg-white p-3 rounded border">
                Error = Y<sub>t</sub> − F<sub>t</sub>
              </p>
              <p className="text-sm text-gray-600 mt-2">Nilai positif = prediksi terlalu rendah, Nilai negatif = prediksi terlalu tinggi</p>
            </div>

            <div>
              <p className="text-sm font-semibold text-gray-700 mb-2">Absolute Error (Nilai Mutlak):</p>
              <p className="font-mono text-lg bg-white p-3 rounded border">
                |Error| = |Y<sub>t</sub> − F<sub>t</sub>|
              </p>
            </div>

            <div>
              <p className="text-sm font-semibold text-gray-700 mb-2">APE (Absolute Percentage Error):</p>
              <p className="font-mono text-lg bg-white p-3 rounded border">
                APE = (|Error| / Y<sub>t</sub>) × 100%
              </p>
              <p className="text-sm text-gray-600 mt-2">Menunjukkan persentase kesalahan prediksi relatif terhadap nilai aktual</p>
            </div>

            <div>
              <p className="text-sm font-semibold text-gray-700 mb-2">MAPE (Mean Absolute Percentage Error):</p>
              <p className="font-mono text-lg bg-white p-3 rounded border">
                MAPE = (Σ APE) / n
              </p>
              <p className="text-sm text-gray-600 mt-2">Rata-rata APE dari seluruh data testing, mengukur akurasi keseluruhan model</p>
            </div>

            <div className="bg-white p-4 rounded border border-orange-300 mt-4">
              <p className="font-semibold text-orange-800 mb-2">Contoh Record No. 2:</p>
              <p className="text-sm">Y<sub>2</sub> = 0.469444, F<sub>2</sub> = 0.104722</p>
              <p className="text-sm">Error = 0.469444 − 0.104722 = <strong>+0.364722</strong></p>
              <p className="text-sm">|Error| = 0.364722</p>
              <p className="text-sm">APE = (0.364722 / 0.469444) × 100% = <strong>77.69%</strong></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
