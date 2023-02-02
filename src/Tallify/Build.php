<?php

namespace Tallify;

use Output;
use Command;
use Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Build
{
    public $files;
    public $config;

    /**
     * Create a new Filesystem class instance.
     *
     * @param  Filesystem  $files
     * @param  Configuration  $config
     */
    public function __construct(Filesystem $files, Config $config)
    {
        $this->files = $files;
        $this->config = $config;
    }

    /**
     * Check if your project exists within your laravel repositories path.
     * @param string $projectName
     *
     * @return boolean
     */
    public function checkApplicationExists($projectName)
    {
        $key = 'laravel-repositories-path';
        $config = $this->config->read();

        $laravelRepositoriesPath = $config[$key];
        $laravelProjects = $this->files->scandir($laravelRepositoriesPath);

        return in_array($projectName, $laravelProjects);
    }

    /**
     * Check if your default stubs exists.
     *
     * @return boolean
     */
    public function checkAllStubsExists()
    {
        return $this->checkIfInStubsDirectory('stubs') && $this->checkIfInStubsDirectory('stubs-directories');
    }

    /**
     * Check if all the files in the Tallify configuration file exists
     * @param string $key
     *
     * @return boolean
     */
    public function checkIfInStubsDirectory($key)
    {
        $config = $this->config->read();
        $defaultStubs = array_keys($config[$key]);

        $stubsToCheckAgainst = $key == 'stubs' ? $this->getFilesFromStubDirectory() : $this->getFoldersFromStubDirectory();

        $results = collect($defaultStubs)->filter(function ($stub) use ($stubsToCheckAgainst) {
            return !in_array($stub, $stubsToCheckAgainst);
        })->all();

        return count($results) === 0;
    }

    /**
     * Get the relevant stubs directory path
     * @param array $config
     *
     * @return string
     */
    public function getStubsPath()
    {
        $key = 'tallify-custom-config';
        $config = $this->config->read();

        return $config[$key] ? ($config[$key . '-path']) . '/stubs' : TALLIFY_SRC_PATH . '/stubs';
    }

    /**
     * Get all files from the stub folder without the directories
     *
     * @return array
     */
    public function getFilesFromStubDirectory()
    {
        $stubPath = $this->getStubsPath();
        $files = $this->files->scandir($stubPath);

        return collect($files)->filter(function ($file) use ($stubPath) {
            return !is_dir("$stubPath/$file");
        })->all();
    }

    /**
     * Get all folders from the stub folder without the files
     *
     * @return array
     */
    public function getFoldersFromStubDirectory()
    {
        $stubPath = $this->getStubsPath();
        $files = $this->files->scandir($stubPath);

        return collect($files)->filter(function ($file) use ($stubPath) {
            return is_dir("$stubPath/$file");
        })->all();
    }

    /**
     * Copy default stubs to fresh Laravel application.
     * @param string $projectName
     *
     * @return void
     *
     */
    public function copyStubsToLaravelProject($projectName)
    {
        $config = $this->config->read();

        $projectPath = $this->getProjectPath($projectName);

        $stubsFiles = $config['stubs'];
        $stubsFolders = $config['stubs-directories'];

        $this->copyFilesFromTo($stubsFiles, $projectPath);
        $this->copyFilesFromTo($stubsFolders, $projectPath, true);
    }

    /**
     * Check wether the Laravel application already has tallify default stubs.
     * @param string $projectName
     *
     * @return boolean
     */
    public function checkIfProjectHasBeenTallifiedAlready($projectName)
    {
        $projectPath = $this->getProjectPath($projectName);
        $config = $this->config->read();
        $stubsFiles = $config['stubs'];

        $stubsFiles = collect($stubsFiles)->filter(function ($item, $key) {
            $filesToExcludesFromCheck = [
                'AppServiceProvider.php',
                'vite.config.js',
                "web.php",
                "app.css",
                "app.js"
            ];

            return !collect($filesToExcludesFromCheck)->contains($key);
        })->all();

        foreach ($stubsFiles as $file => $path) {
            if (is_file("$projectPath/$path/$file")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ask user if a 'tallified' Laravel application can be 'tallified' again.
     * @param Application $that
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return boolean
     *
     */
    public function askUserPermissionToOverride($that, InputInterface $input, OutputInterface $output)
    {
        $question = 'Some files already exists. Would you like to override them? [y/N]';
        $answer = Question::confirm($question, $that, $input, $output);

        return $answer;
    }

    /**
     * Copy default stubs to a fresh Laravel application.
     * @param array $stubs
     * @param string $projectPath
     * @param boolean $folder
     *
     * @return void
     */
    public function copyFilesFromTo($stubs, $projectPath, $folder = null)
    {
        $stubsPath = $this->getStubsPath();

        foreach ($stubs as $file => $path) {
            if ($path !== "/") {
                $this->ensureCompletePathExistsIn($projectPath, $path);
            }

            if ($folder) {
                is_dir("$projectPath/$path/$file")
                    ? Output::oneLiner("<span class='pl-1 text-red-600'>--</span><span class='pr-1 text-green-500'>++</span> Replacing $file")
                    : Output::oneLiner("<span class='pr-1 text-green-500'>++++</span> Adding $file");

                $this->files->mirrorAsUser("$stubsPath/$file", "$projectPath/$path/$file");
            } else {
                is_file("$projectPath/$path/$file")
                    ? Output::oneLiner("<span class='pl-1 text-red-600'>--</span><span class='pr-1 text-green-500'>++</span> Replacing $file")
                    : Output::oneLiner("<span class='px-1 text-green-500'>++++</span> Adding $file");

                $this->files->copyAsUser("$stubsPath/$file", "$projectPath/$path/$file");
            }
        }
    }

    /**
     * Ensure folders to cpy stubs into exists or create it.
     *
     * @param string $projectPath
     * @param string $path
     *
     * @return void
     */
    public function ensureCompletePathExistsIn($projectPath, $path)
    {
        $directory = "";
        $folders = explode('/', $path);

        for ($level = 0; $level < count($folders); $level++) {
            $directory .= $folders[$level];

            $this->files->ensureDirExists("$projectPath/$directory", user());

            $directory .= "/";
        }
    }

    /**
     * Install Tallify default composer packages.
     *
     * @param string $projectName
     *
     * @return Command
     */
    public function installComposerPackages($projectName)
    {
        $composerPackages = $this->config->read()["composer-dependencies"];
        $projectPath = $this->getProjectPath($projectName);
        $composer = $this->findComposer($projectPath);
        $originPath = getcwd();

        $packages = $this->buildPackagesString($composerPackages);

        $commands = count($composerPackages) > 0 ? [
            "cd $projectPath",
            "sudo -u " . user() . " $composer require $packages",
            "cd $originPath",
        ] : [];

        Command::runCommands($commands);
    }

    /**
     * Install Tallify default composer development packages.
     *
     * @param string $projectName
     *
     * @return Command
     */
    public function installComposerDevelopmentPackages($projectName)
    {
        $composerPackages = $this->config->read()["composer-dev-dependencies"];
        $projectPath = $this->getProjectPath($projectName);
        $composer = $this->findComposer($projectPath);
        $originPath = getcwd();

        $packages = $this->buildPackagesString($composerPackages);

        $commands = count($composerPackages) > 0 ? [
            "cd $projectPath",
            "sudo -u " . user() . " $composer require --dev $packages",
            "cd $originPath",
        ] : [];

        Command::runCommands($commands);
    }

    /**
     * Uninstall Tallify default composer packages.
     *
     * @param string $projectName
     *
     * @return Command
     */
    public function uninstallComposerPackages($projectName)
    {
        $composerPackages = $this->config->read()["laravel-default-composer-dependencies-to-remove"];
        $projectPath = $this->getProjectPath($projectName);
        $composer = $this->findComposer($projectPath);
        $originPath = getcwd();

        $packages = $this->buildPackagesString($composerPackages);

        $commands = count($composerPackages) > 0 ? [
            "cd $projectPath",
            "sudo -u " . user() . " $composer remove $packages",
            "cd $originPath",
        ] : [];

        Command::runCommands($commands);
    }

    /**
     * Uninstall Tallify default composer packages.
     *
     * @param string $projectName
     *
     * @return Command
     */
    public function uninstallComposerDevelopmentPackages($projectName)
    {
        $composerPackages = $this->config->read()["laravel-default-composer-dependencies-to-remove"];
        $projectPath = $this->getProjectPath($projectName);
        $composer = $this->findComposer($projectPath);
        $originPath = getcwd();

        $packages = $this->buildPackagesString($composerPackages);

        $commands = count($composerPackages) > 0 ? [
            "cd $projectPath",
            "sudo -u " . user() . " $composer remove --dev $packages",
            "cd $originPath",
        ] : [];

        Command::runCommands($commands);
    }

    /**
     * Add Tallify default npm packages.
     *
     * @param string $projectName
     *
     * @return Command
     */
    public function addNpmPackages($projectName)
    {
        $npmPackages = $this->config->read()["npm-dependencies"];
        $projectPath = $this->getProjectPath($projectName);
        $originPath = getcwd();

        $packages = $this->buildPackagesString($npmPackages);

        $commands = count($npmPackages) > 0 ? [
            "cd $projectPath",
            "sudo -u " . user() . " npm i -D $packages",
            'rm -f package-lock.json',
            'rm -rf node_modules',
            "cd $originPath",
        ] : [];

        Command::runCommands($commands);
    }

    /**
     * Remove Laravel default npm packages based on your Tallify configuration file.
     *
     * @param string $projectName
     *
     * @return Command
     */
    public function removeNpmPackages($projectName)
    {
        $npmPackages = $this->config->read()["laravel-default-npm-dependencies-to-remove"];
        $projectPath = $this->getProjectPath($projectName);
        $originPath = getcwd();

        $packages = $this->buildPackagesString($npmPackages);

        $commands = $commands = count($npmPackages) > 0 ? [
            "cd $projectPath",
            "sudo -u " . user() . " npm uninstall $packages",
            'rm -f package-lock.json',
            'rm -rf node_modules',
            "cd $originPath",
        ] : [];

        Command::runCommands($commands);
    }

    /**
     * Add post update artisan command from the Tallify configuration file.
     *
     * @param string $projectName
     *
     * @return void
     *
     */
    public function addPostUpdateArtisanCommands($projectName)
    {
        $artisanComands = $this->config->read()['post-update-cmd'];
        $projectPath = $this->getProjectPath($projectName);

        $search = '"post-update-cmd": [';
        $replace = "\"post-update-cmd\": [";

        foreach ($artisanComands as $command) {
            $replace .= "\n            \"$command\",";
        }

        $this->files->replaceInFile(
            $search,
            $replace,
            "$projectPath/composer.json",
        );
    }

    /**
     * Run post composer install/update artisan command from the Tallify configuration file.
     *
     * @param string $projectName
     *
     * @return void
     *
     */
    public function runArtisanCommands($projectName)
    {
        $artisanComands = $this->config->read()['artisan-commands'];
        $projectPath = $this->getProjectPath($projectName);
        $originPath = getcwd();

        $commands = [
            "cd $projectPath",
        ];

        foreach ($artisanComands as $command) {
            array_push($commands, "php artisan $command");
        }

        array_push($commands, "cd $originPath");

        Command::runCommands($commands);
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer($projectPath)
    {
        $composerPath = $projectPath . '/composer.phar';

        if (file_exists($composerPath)) {
            return '"' . PHP_BINARY . '" ' . $composerPath;
        }

        return 'composer';
    }

    /**
     * Get the Laravel application path.
     * @param $projectName
     *
     * @return string
     */
    public function getProjectPath($projectName)
    {
        $config = $this->config->read();
        $key = "laravel-repositories-path";
        return "$config[$key]/$projectName";
    }

    /**
     * Create string from packages array
     *
     * @param  array  $packages
     * @return $string
     */
    protected function buildPackagesString(array $packages)
    {
        $str = '';

        foreach ($packages as $package) {
            if (empty($str)) {
                $str = $package;
            } else {
                $str = $str . " " . $package;
            }
        }

        return $str;
    }

    /**
     * Add files to gitignore.
     *
     * @return void
     */
    public function addFilesToGitignore($projectName)
    {
        $projectPath = $this->getProjectPath($projectName);
        $gitignorFiles = $this->config->read()["add-to-gitignore"];
        $gitignorePath = "$projectPath/.gitignore";

        foreach ($gitignorFiles as $files) {
            $this->files->addInFile($files, $gitignorePath);
        }
    }

    /**
     * Add files to .env.
     *
     * @return void
     */
    public function addFilesToDotEnv($projectName)
    {
        $projectPath = $this->getProjectPath($projectName);
        $gitignorFiles = $this->config->read()["add-to-dot-env"];
        $gitignorePath = "$projectPath/.env";

        foreach ($gitignorFiles as $files) {
            $this->files->addInFile($files, $gitignorePath);
        }
    }

    /**
     * Secure app URL for HTTPS
     *
     * @return void
     */
    public function secureAppUrlInDotEnv($projectName)
    {
        $projectPath = $this->getProjectPath($projectName);
        $file = "$projectPath/.env";

        $search = 'APP_URL=http';
        $replace = "APP_URL=https";

        $this->files->replaceInFile(
            $search,
            $replace,
            $file,
        );
    }

    /**
     * Valet secure Laravel Project.
     *
     * @param string $projectName
     *
     * @return void
     *
     */
    public function valetSecure($projectName)
    {
        $commands = [
            "valet secure $projectName",
        ];

        Command::runCommands($commands);
    }
}
