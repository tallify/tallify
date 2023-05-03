import livewire from '@defstudio/vite-livewire-plugin';
import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd())
    const appUrl = env.VITE_APP_URL
    const domain = appUrl.replace('https://', '')

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: false,
                valetTls: domain
            }),
            livewire({
                refresh: ['resources/css/app.css'],
            }),
        ],
    }
})
