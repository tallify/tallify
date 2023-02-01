import livewire from '@defstudio/vite-livewire-plugin';
import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'

const homedir = require("os").homedir();

export default defineConfig(({ mode }) => {
    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: false,
            }),
            livewire({
                refresh: ['resources/css/app.css'],
            }),
        ],
        server: detectServerConfig(mode),
    }
})

function detectServerConfig(mode) {

    if (mode !== 'development') {
        return
    }

    const env = loadEnv(mode, process.cwd())
    const appUrl = env.VITE_APP_URL
    const domain = appUrl.replace('https://', '')
    let browser = false
    let ssl = true

    if (env.VITE_BROWSER) {
        process.env.BROWSER = env.VITE_BROWSER
        browser = true
    }

    return {
        https: {
            key: homedir + "/.config/valet/Certificates/" + domain + ".key",
            cert: homedir + "/.config/valet/Certificates/" + domain + ".crt",
        },
        host: domain,
        open: browser && appUrl,
    }
}
