<?php

namespace Tallify;

use Output;
use Command;
use Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Config
{
    public $files;

    /**
     * Create a new Filesystem class instance.
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
        $config = $this->read();

        if ($config['tallify-custom-config']) {
            $dir = $config['tallify-custom-config-path'];
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
     * @param Application $that
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function resetConfigurationFile($that, InputInterface $input, OutputInterface $output)
    {
        $config = $this->read();
        $dir = $config['tallify-custom-config-path'];

        if (is_dir($dir)) {
            $question = "It looks like you published the Tallify configuration. Would you like to remove it as well? [y/N]";
            $answer = Question::confirm($question, $that, $input, $output);

            if ($answer) {
                $this->files->rmDirAndContents($dir);
            }
        }

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
                'laravel-repositories-path'             => "",
                'tallify-custom-config'                 => false,
                'tallify-custom-config-path'            => "",
                'composer-dependencies'                 => [
                    'livewire/livewire',
                ],
                'composer-dev-dependencies'             => [
                    "pestphp/pest-plugin-parallel",
                    "pestphp/pest-plugin-livewire",
                    "barryvdh/laravel-ide-helper",
                    "pestphp/pest-plugin-laravel",
                    "barryvdh/laravel-debugbar",
                    "nunomaduro/larastan",
                    "pestphp/pest",
                    "laravel/pint",
                ],
                "laravel-default-composer-dependencies-to-remove"    => [],
                "laravel-default-composer-dev-dependencies-to-remove"    => [],
                "artisan-commands"                      => [],
                "post-update-cmd"                       => [],
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
                "laravel-default-npm-dependencies-to-remove"    => [],
                'stubs'                                 => [
                    // "stub file"              => "path/to/install/file"
                    "AppServiceProvider.php"    => "app/Providers",
                    "tailwind.config.js"        => "/",
                    "postcss.config.js"         => "/",
                    "vite.config.js"            => "/",
                    "app.blade.php"             => "resources/views/layouts",
                    "AppLayout.php"             => "app/View/Components",
                    "phpstan.neon"              => "/",
                    "tailwind.css"              => "resources/css/libraries",
                    "livewire.js"               => "resources/js/libraries",
                    "alpine.css"                => "resources/css/libraries",
                    "inter.css"                 => "resources/css/fonts",
                    "alpine.js"                 => "resources/js/libraries",
                    "vite.js"                   => "resources/js/libraries",
                    "app.css"                   => "resources/css",
                    "app.js"                    => "resources/js",
                ],
                'stubs-directories'                             => [],
                'add-to-gitignore'                              => [
                    "_ide_helper_models",
                    "_ide_helper",
                    ".DS_Store",
                    ".env.staging",
                ],
                "add-to-dot-env"                                => [
                    'VITE_APP_URL="${APP_URL}"',
                    'VITE_BROWSER="google-chrome"',
                    'VITE_LIVEWIRE_OPT_IN=true',
                ],
            ]);
        }
    }

    /**
     * Publish default configuration to a given path for customisation
     *
     * @param string $path
     * @param Application $that
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param boolean $force
     *
     * @return void
     */
    public function publishFiles($path, $that, InputInterface $input, OutputInterface $output, $force = null)
    {
        $config = $this->read();

        $dir = $config['tallify-custom-config-path'];

        if (is_dir($dir) && !$force) {
            $question = "It looks like this path already exists. Would you like to carry on? [y/N]";
            $answer = Question::confirm($question, $that, $input, $output);

            if ($answer == false) {
                return Output::italicSingle(
                    "Process canceled. Your tallify configuration has <span class='font-bold underline'>NOT</span> been published.",
                    'warning',
                );
            }
        }

        $config['tallify-custom-config'] = true;

        $config['tallify-custom-config-path'] = removeTrailingSlash($path) . '/tallify';

        $this->write($config);

        if (!$this->files->isDir($path . '/tallify')) {
            $this->files->ensureDirExists($path . '/tallify', user());
            $this->files->ensureDirExists($path . '/tallify/stubs', user());
        }

        $customConfigPath = $config['tallify-custom-config-path'];
        $defaultStubsPath = __DIR__ . '/../stubs/';
        $destinationStubsPath = $customConfigPath . '/stubs';

        $this->files->copy(TALLIFY_HOME_PATH . '/config.json', $customConfigPath . '/config.json');

        foreach ($this->files->scandir($defaultStubsPath) as $stub) {
            $this->files->copy($defaultStubsPath . '/' . $stub, $destinationStubsPath . '/' . $stub);
        }
    }

    /**
     * Remove published configuration directory and files
     *
     * @return void
     */
    public function removePublishedFiles()
    {
        $key = 'tallify-custom-config-path';
        $config = $this->read();

        $this->files->rmDirAndContents($config[$key]);
    }

    /**
     * Get the path to the custom Tallify configuration file.
     *
     * @return string
     */
    public function getPublishedPath()
    {
        $config = $this->read();

        return $config['tallify-custom-config-path'];
    }

    /**
     * Check if Tallify directory and files have been published already
     *
     * @return boolean
     */
    public function checkIfConfigurationHasBeenPublished()
    {
        $key = 'tallify-custom-config';
        $config = $this->read();

        return $config[$key];
    }

    /**
     * Get the path to the Tallify configuration file.
     *
     * @return string
     */
    public function getParkedPath()
    {
        $config = $this->read();

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
        $config = $this->read();

        $config['laravel-repositories-path'] = removeTrailingSlash($path);

        $this->write($config);
    }

    /**
     * Add a package to the default Tallify configuration file.
     *
     * @param string $key
     * @param string $packageName
     *
     * @return void
     */
    public function addPackageTo($key, $packageName)
    {
        $config = $this->read();

        array_push($config[$key], $packageName);

        $this->write($config);
    }

    /**
     * Remove a package to the default Tallify configuration file.
     * @param string $key
     * @param string $packageName
     *
     * @return void
     */
    public function removePackageFrom($key, $packageName)
    {
        $config = $this->read();

        array_splice($config[$key], array_search($packageName, $config[$key]), 1);

        $this->write($config);
    }

    /**
     * Displays default composer or npm packages from the tallify configuration file.
     *
     * @param string $key
     *
     * @return array
     */
    public function displayDefaultPackagesFor($key)
    {
        $config = $this->read();

        return $config[$key];
    }

    /**
     * Check if package already is in the tallify configuration file.
     *
     * @param string $key
     * @param string $packageName
     *
     * @return boolean
     */
    public function checkIfPackageIsInConfigurationFile($key, $packageName)
    {
        $config = $this->read();

        return in_array($packageName, $config[$key]);
    }

    /**
     * Check if package exists.
     *
     * @param string $key
     * @param string $packageName
     *
     * @return boolean
     */
    public function checkIfPackageExists($key, $packageName)
    {
        if ($key == "composer-dependencies" || $key == "composer-dev-dependencies") {
            if (!str_contains($packageName, "/")) {
                return false;
            }

            // Check if repository exists
            $status = Command::cUrl("https://github.com/$packageName");

            if ($status == "200") {
                return true;
            }
        }

        if ($key == "npm-dependencies") {
            // Check if repository exists
            $status = Command::cUrl("https://www.npmjs.com/package/$packageName");

            if ($status == "200") {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a stub to the default Tallify configuration file.
     *
     * @param string $stubName
     * @param string $stubPath
     * @param boolean $directoy
     *
     * @return void
     */
    public function addStub($stubName, $stubPath, $directory)
    {
        $key = $directory ? 'stubs-directories' : 'stubs';
        $config = $this->read();

        $sanitisedPath = $stubPath === "/" ? $stubPath : removeLeadingSlash(removeTrailingSlash($stubPath));

        $config[$key][$stubName] = $sanitisedPath;

        $this->write($config);
    }

    /**
     * Remove a stub to the default Tallify configuration file.
     *
     * @param string $stubName
     *
     * @return void
     */
    public function removeStub($stubName)
    {
        $key = 'stubs';
        $config = $this->read();

        unset($config[$key][$stubName]);

        $this->write($config);
    }

    /**
     * Check if stub already is in the tallify configuration file.
     *
     * @param string $stubName
     * @param boolean $directory
     *
     * @return boolean
     */
    public function checkIfStubIsInConfigurationFile($stubName, $directory)
    {
        $key = $directory ? 'stubs-directories' : 'stubs';
        $config = $this->read();

        return array_key_exists($stubName, $config[$key]);
    }

    /**
     * Displays default stubs from the tallify configuration file.
     *
     * @param boolean $directory
     *
     * @return array
     */
    public function displayDefaultStubs($directory)
    {
        $key = $directory ? 'stubs-directories' : 'stubs';
        $config = $this->read();

        return $config[$key];
    }

    /**
     * Check if artisan command already is in the tallify configuration file.
     *
     * @param string $command
     *
     * @return boolean
     */
    public function checkIfArtisanCommandIsInConfigurationFile($command, $postUpdate)
    {
        $key = $postUpdate ? 'post-update-cmd' : 'artisan-commands';

        $config = $this->read();

        return in_array($command, $config[$key]);
    }

    /**
     * Add an artisan command to the default Tallify configuration file.
     *
     * @param string $command
     *
     * @return void
     */
    public function addArtisanCommand($command, $postUpdate)
    {
        $key = $postUpdate ? 'post-update-cmd' : 'artisan-commands';

        $config = $this->read();

        array_push($config[$key], $command);

        $this->write($config);
    }

    /**
     * Remove an artisan command to the default Tallify configuration file.
     *
     * @param string $command
     *
     * @return void
     */
    public function removeArtisanCommand($command, $postUpdate)
    {
        $key = $postUpdate ? 'post-update-cmd' : 'artisan-commands';

        $config = $this->read();

        array_splice($config[$key], array_search($command, $config[$key]), 1);

        $this->write($config);
    }

    /**
     * Displays all artisan commands from the Tallify configuration file.
     *
     * @return array
     */
    public function displayDefaultArtisanCommands($postUpdate)
    {
        $key = $postUpdate ? 'post-update-cmd' : 'artisan-commands';
        $config = $this->read();

        return $config[$key];
    }

    /**
     * Check if environment variable already is in the add-to-dot-env tallify configuration file.
     *
     * @param string $env
     *
     * @return boolean
     */
    public function checkIfVariableIsInConfigurationFile($env)
    {
        $key = 'add-to-dot-env';

        $config = $this->read();

        return in_array($env, $config[$key]);
    }

    /**
     * Add an environment variable to the add-to-dot-env Tallify configuration file.
     *
     * @param string $envVariable
     *
     * @return void
     */
    public function addEnvVariable($envVariable)
    {
        $key = 'add-to-dot-env';

        $config = $this->read();

        array_push($config[$key], $envVariable);

        $this->write($config);
    }

    /**
     * Remove an environment variable to the add-to-dot-env Tallify configuration file.
     *
     * @param string $envVariable
     *
     * @return void
     */
    public function removeEnvVariable($envVariable)
    {
        $key = 'add-to-dot-env';

        $config = $this->read();

        array_splice($config[$key], array_search($envVariable, $config[$key]), 1);

        $this->write($config);
    }

    /**
     * Displays all environment variables from the add-to-dot-env Tallify configuration file.
     *
     * @return array
     */
    public function displayDefaultEnvironmentVariables()
    {
        $key = 'add-to-dot-env';

        $config = $this->read();

        return $config[$key];
    }

    /**
     * Check if file already is in the add-to-gitignore tallify configuration file.
     *
     * @param string $filePath
     *
     * @return boolean
     */
    public function checkIfFileIsInConfigurationFile($filePath)
    {
        $key = 'add-to-gitignore';

        $config = $this->read();

        return in_array($filePath, $config[$key]);
    }

    /**
     * Add file to the add-to-gitignore Tallify configuration file.
     *
     * @param string $filePath
     *
     * @return void
     */
    public function addFileToGitignore($filePath)
    {
        $key = 'add-to-gitignore';

        $config = $this->read();

        array_push($config[$key], $filePath);

        $this->write($config);
    }

    /**
     * Remove file to the add-to-gitignore Tallify configuration file.
     *
     * @param string $filePath
     *
     * @return void
     */
    public function removeFileFromGitignore($filePath)
    {
        $key = 'add-to-gitignore';

        $config = $this->read();

        array_splice($config[$key], array_search($filePath, $config[$key]), 1);

        $this->write($config);
    }

    /**
     * Displays all files from the add-to-gitignore Tallify configuration file.
     *
     * @return array
     */
    public function displayDefaultGitignoreFiles()
    {
        $key = 'add-to-gitignore';

        $config = $this->read();

        return $config[$key];
    }

    /**
     * Check wether the user has given the path to his/her Laravel repositories
     *
     * @return boolean
     */
    public function checkIfParked()
    {
        $config = $this->read();

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
    public function read()
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

        if (!is_dir($config[$key . '-path'])) {
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
        return $config['tallify-custom-config-path'] . '/config.json';
    }

    /**
     * Get Tallify configuration file key for a given library
     */
    public function getLibraryConfigKeyAttribute($library, $dev = null, $remove = null)
    {
        if ($remove) {
            return $library == 'npm' ? 'laravel-default-npm-dependencies-to-remove' : ($dev ? 'laravel-default-composer-dev-dependencies-to-remove' : 'laravel-default-composer-dependencies-to-remove');
        }
        return $library == 'npm' ? 'npm-dependencies' : ($dev ? 'composer-dev-dependencies' : 'composer-dependencies');
    }
}
