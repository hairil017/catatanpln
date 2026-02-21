import { useState } from 'react';
import { FileSpreadsheet, Calculator, BookOpen, BarChart3, Settings } from 'lucide-react';
import DataSheet from './components/DataSheet';
import ParametersSheet from './components/ParametersSheet';
import CalculationsSheet from './components/CalculationsSheet';
import FormulasSheet from './components/FormulasSheet';
import ResultsSheet from './components/ResultsSheet';

type SheetType = 'data' | 'parameters' | 'calculations' | 'formulas' | 'results';

function App() {
  const [activeSheet, setActiveSheet] = useState<SheetType>('data');

  const sheets = [
    { id: 'data', name: 'Data Mentah', icon: FileSpreadsheet },
    { id: 'parameters', name: 'Parameter & Inisialisasi', icon: Settings },
    { id: 'calculations', name: 'Holt-Winters Lengkap', icon: Calculator },
    { id: 'formulas', name: 'Penjelasan Rumus', icon: BookOpen },
    { id: 'results', name: 'Hasil & MAPE', icon: BarChart3 },
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="bg-gradient-to-r from-green-700 to-green-600 text-white py-6 px-6 shadow-lg">
        <h1 className="text-3xl font-bold mb-2">Template Perhitungan Holt-Winters Per-Record</h1>
        <p className="text-green-100">PLN Galesong - Perbaikan Meteran 2025</p>
      </div>

      <div className="border-b border-gray-300 bg-white shadow-sm">
        <div className="max-w-7xl mx-auto">
          <div className="flex overflow-x-auto">
            {sheets.map((sheet) => {
              const Icon = sheet.icon;
              return (
                <button
                  key={sheet.id}
                  onClick={() => setActiveSheet(sheet.id as SheetType)}
                  className={`flex items-center gap-2 px-6 py-4 font-medium border-b-2 transition-all whitespace-nowrap ${
                    activeSheet === sheet.id
                      ? 'border-orange-500 text-orange-600 bg-orange-50'
                      : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                  }`}
                >
                  <Icon size={18} />
                  {sheet.name}
                </button>
              );
            })}
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto p-6">
        {activeSheet === 'data' && <DataSheet />}
        {activeSheet === 'parameters' && <ParametersSheet />}
        {activeSheet === 'calculations' && <CalculationsSheet />}
        {activeSheet === 'formulas' && <FormulasSheet />}
        {activeSheet === 'results' && <ResultsSheet />}
      </div>
    </div>
  );
}

export default App;
