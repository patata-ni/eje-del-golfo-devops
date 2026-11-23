import { mkdir, writeFile } from 'node:fs/promises';
import assert from 'node:assert/strict';
const base = process.env.BASE_URL;
const expected = process.env.EXPECTED_COMMIT;
assert.ok(base && expected, 'BASE_URL y EXPECTED_COMMIT son obligatorios.');
let latest;
for (let i = 0; i < 90; i++) {
  try {
    const response = await fetch(new URL(`/version?commit=${expected}`, base), { signal: AbortSignal.timeout(12000) });
    assert.ok(response.ok, `HTTP ${response.status}`);
    const version = await response.json();
    assert.equal(version.status, 'ok');
    assert.equal(version.commit, expected);
    if (process.env.EXPECTED_SOURCE_HASH) assert.equal(version.source_hash, process.env.EXPECTED_SOURCE_HASH);
    await mkdir('artifacts', { recursive: true });
    await writeFile('artifacts/deployed-version.json', JSON.stringify({ url: base, ...version }, null, 2));
    console.log(`Versión y conexión a MySQL verificadas: ${expected}`);
    break;
  } catch (error) {
    latest = error;
    if (i === 89) throw latest;
    console.log(`Esperando versión validada en Railway, intento ${i + 1}/90.`);
    await new Promise(resolve => setTimeout(resolve, 5000));
  }
}
