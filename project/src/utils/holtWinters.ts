export interface HoltWintersParams {
  alpha: number;
  beta: number;
  gamma: number;
}

export interface HoltWintersResult {
  t: number;
  Y: number;
  L: number;
  T: number;
  S: number;
  F: number;
  error: number | null;
  absError: number | null;
  ape: number | null;
  status: 'init' | 'training' | 'testing';
}

export function calculateHoltWinters(
  data: number[],
  params: HoltWintersParams,
  trainingSize: number
): HoltWintersResult[] {
  const { alpha, beta, gamma } = params;
  const results: HoltWintersResult[] = [];

  const first5 = data.slice(0, 5);
  const L0 = first5.reduce((sum, val) => sum + val, 0) / 5;

  const last5Training = data.slice(trainingSize - 5, trainingSize);
  const avgFirst5 = first5.reduce((sum, val) => sum + val, 0) / 5;
  const avgLast5 = last5Training.reduce((sum, val) => sum + val, 0) / 5;
  const T0 = (avgLast5 - avgFirst5) / (trainingSize - 5);

  const S0 = data[0] - (L0 + T0);

  results.push({
    t: 1,
    Y: data[0],
    L: L0,
    T: T0,
    S: S0,
    F: L0 + T0 + S0,
    error: null,
    absError: null,
    ape: null,
    status: 'init'
  });

  for (let t = 1; t < data.length; t++) {
    const prev = results[t - 1];
    const Y_t = data[t];

    const F_t = prev.L + prev.T + prev.S;

    const L_t = alpha * (Y_t - prev.S) + (1 - alpha) * (prev.L + prev.T);
    const T_t = beta * (L_t - prev.L) + (1 - beta) * prev.T;
    const S_t = gamma * (Y_t - L_t) + (1 - gamma) * prev.S;

    const error = Y_t - F_t;
    const absError = Math.abs(error);
    const ape = Y_t !== 0 ? (absError / Y_t) * 100 : 0;

    const status = t < trainingSize ? 'training' : 'testing';

    results.push({
      t: t + 1,
      Y: Y_t,
      L: L_t,
      T: T_t,
      S: S_t,
      F: F_t,
      error,
      absError,
      ape,
      status
    });
  }

  return results;
}

export function calculateMAPE(results: HoltWintersResult[], startIndex: number): number {
  const testingData = results.slice(startIndex);
  const validAPEs = testingData.filter(r => r.ape !== null && !isNaN(r.ape));

  if (validAPEs.length === 0) return 0;

  const sumAPE = validAPEs.reduce((sum, r) => sum + (r.ape || 0), 0);
  return sumAPE / validAPEs.length;
}
