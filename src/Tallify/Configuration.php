<?php

namespace Tallify;

use Command;

class Configuration
{
    public $files;

    /**
     * Create a new Valet configuration class instance.
     *
     * @param  Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Install the Tallify configuration file.
     *
     * @return void
     */
    public function install()
    {
        $this->createConfigurationDirectory();
        $this->writeBaseConfiguration();

        $this->files->chown($this->defaultPath(), user());
    }

    /**
     * Uninstall Tallify services.
     *
     * @return void
     */
    public function uninstall()
    {
        $config = $this->readConfig();

        if ($config['tallify-custom-config']) {
            $dir = $config['tallify-custom-config-path'] . '/tallify';
            if (is_dir($dir)) {
                $this->files->rmDirAndContents($dir);
            }
        }

        return $this->files->rmDirAndContents(TALLIFY_HOME_PATH);
    }

    /**
     * Create the Tallify configuration directory.
     *
     * @return void
     */
    public function createConfigurationDirectory()
    {
        $this->files->ensureDirExists(TALLIFY_HOME_PATH, user());
    }

    /**
     * Reset the Tallify configuration file to its default state.
     *
     * @return void
     */
    public function resetConfigurationFile()
    {
        $this->removeConfigurationFile();

        $this->createConfigurationDirectory();
        $this->writeBaseConfiguration();

        $this->files->chown($this->defaultPath(), user());
    }

    /**
     * Remove the Tallify configuration file.
     *
     * @return void
     */
    public function removeConfigurationFile()
    {
        $this->files->rmDirAndContents(TALLIFY_HOME_PATH);
    }

    /**
     * Write the base, initial configuration for Tallify.
     *
     * @return void
     */
    public function writeBaseConfiguration()
    {
        if (!$this->files->exists($this->defaultPath())) {
            $this->write([
                'laravel-repositories-path'                => "",
                'tallify-custom-config'             => false,
                'tallify-custom-config-path'        => "",
                'composer-dependenciez'                 => [
                    'livewire/livewire',
                ],
                'composer-dev-dependecies'     => [
                    "pestphp/pest-plugin-parallel",
                    "pestphp/pest-plugin-livewire",
                    "barryvdh/laravel-ide-helper",
                    "pestphp/pest-plugin-laravel",
                    "barryvdh/laravel-debugbar",
                    "nunomaduro/larastan",
                    "pestphp/pest",
                    "laravel/pint",
                ],
                'npm-dependencies'                      => [
                    "@defstudio/vite-livewire-plugin",
                    "@tailwindcss/aspect-ratio",
                    "@tailwindcss/typography",
                    "@tailwindcss/line-clamp",
                    "autoprefixer@latest",
                    "@alpinejs/intersect",
                    "tailwindcss@latest",
                    "@alpinejs/collapse",
                    "@tailwindcss/forms",
                    "@alpinejs/persist",
                    "@alpinejs/focus",
                    "@alpinejs/morph",
                    "@alpinejs/mask",
                    "postcss@latest",
                    "@alpinejs/ui",
                    "alpinejs",
                ],
                'stubs'                   => [
                    // "stub file"              => "path/to/install/file"
                    "AppServiceProvider.php"    => "app/Providers",
                    "tailwind.config.js"        => "/",
                    "postcss.config.js"         => "/",
                    "vite.config.js"            => "/",
                    "app.blade.php"             => "resources/layouts",
                    "AppLayout.php"             => "app/Views/Components",
                    "phpstan.neon"              => "/",
                    "tailwind.css"              => "resources/css/libraries",
                    "livewire.js"               => "resources/js",
                    "alpine.css"                => "resources/css/libraries",
                    "inter.css"                 => "resources/css/fonts",
                    "alpine.js"                 => "resources/js/libraries",
                    "vite.js"                   => "resources/js/libraries",
                    "app.css"                   => "resources/css",
                    "app.js"                    => "resources/js",
                    "web.js"                    => "routes",
                ],
            ]);
        }
    }

    /**
     * Publish default configuration to a given path for customisation
     *
     * @param string $path
     *
     * @return void
     */
    public function publishFiles($path)
    {
        $config = $this->readConfig();

        $config['tallify-custom-config'] = true;

        $config['tallify-custom-config-path'] = removeTrailingSlash($path);

        $this->write($config);

        if (!$this->files->isDir($path . '/tallify')) {
            $this->files->ensureDirExists($path . '/tallify', user());
            $this->files->ensureDirExists($path . '/tallify/stubs', user());
        }

        $configPath = $path . "/tallify";
        $defaultStubsPath = __DIR__ . '/../stubs/';
        $destinationStubsPath = $path . '/tallify/stubs/';

        $this->files->copy(TALLIFY_HOME_PATH . '/config.json', $configPath . '/config.json');

        foreach ($this->files->scandir($defaultStubsPath) as $stub) {
            $this->files->copy($defaultStubsPath . '/' . $stub, $destinationStubsPath . $stub);
        }
    }

    /**
     * Get the path to the Tallify configuration file.
     *
     * @param  string  $path
     *
     * @return void
     */
    public function getParkedPath()
    {
        $config = $this->readConfig();

        return $config['laravel-repositories-path'];
    }

    /**
     * Add the given path to the Tallify configuration file.
     *
     * @param  string  $path
     *
     * @return void
     */
    public function setParkedPath($path)
    {
        $config = $this->readConfig();

        $config['laravel-repositories-path'] = removeTrailingSlash($path);

        $this->write($config);
    }

    /**
     * Add a package to the default Tallify configuration file.
     *
     * @param string $library
     * @param string $packageName
     * @param string $dev
     *
     * @return void
     */
    public function addPackageTo($library, $packageName, $dev)
    {
        $key = $library == 'npm' ? 'npm-packages' : ($dev ? 'composer-development-packages' : 'composer-packages');
        $config = $this->readConfig();

        array_push($config[$key], $packageName);

        $this->write($config);
    }

    /**
     * Remove a package to the default Tallify configuration file.
     *
     * @param string $library
     * @param string $packageName
     * @param string $dev
     *
     * @return boolean
     */
    public function removePackageFrom($library, $packageName, $dev)
    {
        $key = $library == 'npm' ? 'npm-packages' : ($dev ? 'composer-development-packages' : 'composer-packages');
        $config = $this->readConfig();

        if (in_array($packageName, $config[$key])) {
            array_splice($config[$key], array_search($packageName, $config[$key]), 1);

            $this->write($config);

            return true;
        }

        return false;
    }

    public function displayDefaultPackagesFor($library, $dev)
    {
        $key = $library == 'npm' ? 'npm-packages' : ($dev ? 'composer-development-packages' : 'composer-packages');
        $config = $this->readConfig();

        return $config[$key];
    }

    public function displayDefaultStubs()
    {
        $config = $this->readConfig();

        return $config['stubs'];
    }

    /**
     * Check if package already is in the tallify configuration file.
     *
     * @param string $library
     * @param string $packageName
     * * @param string $dev
     *
     * @return boolean
     */
    public function checkIfPackageIsInConfigurationFile($library, $packageName, $dev)
    {
        $key = $library == 'npm' ? 'npm-packages' : ($dev ? 'composer-development-packages' : 'composer-packages');
        $config = $this->readConfig();

        return in_array($packageName, $config[$key]);
    }

    /**
     * Check if package exists.
     *
     * @param string $library
     * @param string $packageName
     *
     * @return boolean
     */
    public function checkIfPackageExists($library, $packageName)
    {
        if ($library == "composer") {
            if (!str_contains($packageName, "/")) {
                return false;
            }
            $status = Command::cUrl("https://github.com/$packageName");

            if ($status == "200") {
                return true;
            }
        }

        if ($library == "npm") {
            $status = Command::cUrl("https://www.npmjs.com/package/$packageName");

            if ($status == "200") {
                return true;
            }
        }

        return false;
    }

    /**
     * Check wether the user has given the path to his/her Laravel repositories
     *
     * @return boolean
     */
    public function checkIfParked()
    {
        $config = $this->readConfig();

        if (is_null($config)) {
            return false;
        }

        $isParked = $config['laravel-repositories-path'];

        return !empty($isParked) && is_dir($isParked);
    }

    /**
     * Read the configuration file as JSON.
     *
     * @return array
     */
    public function readConfig()
    {
        $path = $this->getPath();

        return json_decode($this->files->get($path), true);
    }

    /**
     * Write the given configuration to disk.
     *
     * @param  array  $config
     *
     * @return void
     */
    public function write($config)
    {
        $this->files->putAsUser($this->getPath(), json_encode(
            $config,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ) . PHP_EOL);
    }

    /**
     * Get the configuration file path
     *
     * @return string
     */
    public function getPath()
    {
        // if the default config file does not exist, use its path to create it.
        if (!is_file($this->defaultPath())) {
            return $this->defaultPath();
        }

        $key = 'tallify-custom-config';
        $config = json_decode($this->files->get($this->defaultPath()), true);

        // if user messed up the config files and delete array key $config['tallify-custom-config']
        if (is_null($config) || !array_key_exists($key, $config)) {
            return $this->defaultPath();
        }

        if (!is_dir($config[$key . '-path'] . '/tallify')) {
            return $this->defaultPath();
        }

        return $config[$key] ? $this->customPath($config) : $this->defaultPath();
    }

    /**
     * Get the configuration file path.
     *
     * @return string
     */
    public function defaultPath()
    {
        return TALLIFY_HOME_PATH . '/config.json';
    }

    /**
     * Get the custome configuration file path.
     *
     * @return string
     */
    public function customPath($config)
    {
        return $config['tallify-custom-config-path'] . '/tallify/config.json';
    }
}
