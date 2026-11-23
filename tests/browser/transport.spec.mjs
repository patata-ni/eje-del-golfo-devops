import { test, expect } from '@playwright/test';

const accounts = {
  client: { email: process.env.E2E_CLIENT_EMAIL || 'client.ci@ejegolfo.example', password: process.env.E2E_CLIENT_PASSWORD || 'ci-only-EG-Password-2026' },
  admin: { email: process.env.E2E_ADMIN_EMAIL || 'admin.ci@ejegolfo.example', password: process.env.E2E_ADMIN_PASSWORD || 'ci-only-EG-Password-2026' },
  mechanic: { email: process.env.E2E_MECHANIC_EMAIL || 'mechanic.ci@ejegolfo.example', password: process.env.E2E_MECHANIC_PASSWORD || 'ci-only-EG-Password-2026' },
};
async function login(page, role) {
  await page.goto('/login');
  await page.getByLabel('Correo electrónico').fill(accounts[role].email);
  await page.getByLabel('Contraseña', { exact: true }).fill(accounts[role].password);
  await page.getByRole('button', { name: 'Ingresar', exact: true }).click();
  await expect(page).not.toHaveURL(/\/login$/);
}
test('inicio y ayuda se muestran sin desbordamiento horizontal', async ({ page }) => {
  await page.goto('/');
  await expect(page.getByRole('heading', { name: 'Tu próximo destino comienza aquí.' })).toBeVisible();
  expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
  await page.goto('/ayuda');
  await expect(page.locator('main')).toBeVisible();
});
test('usuario cliente consulta y filtra las salidas', async ({ page }) => {
  await login(page, 'client');
  await page.goto('/rutas');
  await expect(page.getByRole('heading', { name: 'Rutas y Horarios', exact: true })).toBeVisible();
  await expect(page.locator('.run-card').first()).toBeVisible();
  await page.goto('/rutas?destino=DestinoInexistenteDePrueba');
  await expect(page.getByRole('heading', { name: 'No se encontraron salidas' })).toBeVisible();
});
test('administración y mantenimiento se separan por rol', async ({ page }) => {
  await login(page, 'admin');
  await expect(page.getByRole('heading', { name: 'Resumen de Operaciones', exact: true })).toBeVisible();
  await page.goto('/admin/routes');
  await expect(page.locator('main')).toBeVisible();
});
test('mecánico consulta el taller y no accede a administración', async ({ page }) => {
  await login(page, 'mechanic');
  await expect(page.getByRole('heading', { name: 'Dashboard de Taller', exact: true })).toBeVisible();
  const forbidden = await page.goto('/admin');
  expect(forbidden.status()).toBe(403);
});
test('cliente no tiene acceso al módulo administrativo', async ({ page }) => {
  await login(page, 'client');
  const forbidden = await page.goto('/admin');
  expect(forbidden.status()).toBe(403);
  const status = await page.request.get('/version');
  expect(status.ok()).toBe(true);
  const version = await status.json();
  expect(version.status).toBe('ok');
  if (process.env.EXPECTED_COMMIT) expect(version.commit).toBe(process.env.EXPECTED_COMMIT);
});
