import livewire from '@defstudio/vite-livewire-plugin';
import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import { resolve } from 'path'
import { homedir } from 'os'
import fs from 'fs';

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

    let appUrl = env.VITE_APP_URL
    let certificatePath
    let browser = false
    let ssl = false
    let keyPath
    let host

    if (env.VITE_BROWSER) {
        process.env.BROWSER = env.VITE_BROWSER
        browser = true
    }

    if (appUrl.indexOf("http://") == 0) {
        host = appUrl.replace('http://', '')
    }

    if (appUrl.indexOf("https://") == 0) {
        host = appUrl.replace('https://', '')
        ssl = true
        keyPath = resolve(homedir(), `.config/valet/Certificates/${host}.key`)
        certificatePath = resolve(homedir(), `.config/valet/Certificates/${host}.crt`)

        if (!fs.existsSync(keyPath)) {
            throw new Error("File " + keyPath + " not found.");
        }

        if (!fs.existsSync(certificatePath)) {
            throw new Error("File " + certificatePath + " not found.");
        }
    }

    return {
        host,
        https: ssl,
        open: browser && appUrl,
    }
}
