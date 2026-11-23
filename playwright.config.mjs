import { defineConfig, devices } from '@playwright/test';
export default defineConfig({
  testDir: './tests/browser',
  timeout: 45000,
  retries: process.env.CI ? 1 : 0,
  workers: 2,
  reporter: [['list'], ['html', { open: 'never' }], ['junit', { outputFile: 'test-results/browser.xml' }]],
  use: { baseURL: process.env.BASE_URL || 'http://127.0.0.1:8085', screenshot: 'on', trace: 'off' },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    { name: 'firefox', use: { ...devices['Desktop Firefox'] } },
    { name: 'webkit', use: { ...devices['Desktop Safari'] } },
    { name: 'movil', use: { ...devices['Pixel 7'] } },
  ],
  webServer: process.env.BASE_URL ? undefined : {
    command: 'php artisan serve --host=127.0.0.1 --port=8085',
    url: 'http://127.0.0.1:8085/up',
    reuseExistingServer: !process.env.CI,
    timeout: 60000,
  },
});
