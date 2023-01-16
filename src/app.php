<?php

use Silly\Application;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Load correct autoloader depending on install location.
 */
if (file_exists(__DIR__ . '/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require_once getenv('HOME') . '/.composer/vendor/autoload.php';
}

/**
 * Create the application.
 */
$version = '0.1.18';

$app = new Application('Tallify installer', $version);

/**
 * Install Tallify and any required services.
 */
$app->command('install', function () {
    Configuration::install();

    Output::italicSingle(
        "Tallify was successfully installed.",
        'success'
    );

    return Output::italicSingle(
        "Please, go to the folder where you keep all of your Laravel projects and use the <span class='px-1 font-bold text-white bg-gray-800'>tallify park</span> command.",
        'required'
    );
})->descriptions('Install the Tallify services');

/**
 * All commands are available only if tallify is installed.
 */
if (is_dir(TALLIFY_HOME_PATH)) {
    /**
     * Install Tallify and any required services.
     */
    $app->command('uninstall', function (InputInterface $input, OutputInterface $output) {
        $question = 'Are you sure you want to uninstall Tallify? [y/N]';
        $answer = Question::confirm($question, $this, $input, $output);

        if ($answer == false) {
            return Output::italicSingle(
                "Process canceled. Tallify services have <span class='font-bold underline'>NOT</span> been uninstalled.",
                'warning'
            );
        }

        Configuration::uninstall();

        return Output::italicSingle(
            "Tallify was successfully installed.",
            'success'
        );
    })->descriptions('Uninstall the Tallify services');

    /**
     * Tell Tallify where all your Laravel applications live.
     */
    $app->command('park [path]', function ($path = null) {
        Configuration::setParkedPath($path ?: getcwd());

        return Output::italicSingle(
            "Tallify is ready to work its magic!",
            'success',
        );
    })->descriptions('Tells tallify where all your Laravel applications lives to be able to "Tallify" them.');

    /**
     * Returns the path to the user folder where all Laravel applications live.
     */
    $app->command('parked', function () {
        $parkedPath = Configuration::getParkedPath();

        if (empty($parkedPath)) {
            return Output::italicSingle(
                "It looks like you did not set the path to your Laravel applications.",
                'warning',
            );
        }

        return Output::italicSingle(
            "<span class='font-bold underline'>$parkedPath</span> is where your laravel applications live.",
            'info',
        );
    })->descriptions('Shows the directory where all your Laravel applivations live.');

    /**
     * Publish Tallify configuration and files to a given path.
     */
    $app->command('publish [path] [--force]', function (InputInterface $input, OutputInterface $output, $path = null, $force = null) {
        if (!$path) {
            $question = 'Is ' . getcwd() . ' the folder where you develop all your laravel apps? [y/N]';
            $answer = Question::confirm($question, $this, $input, $output);

            if ($answer == false) {
                return Output::italicSingle(
                    "Go to your preferred location for the published files <span class='font-bold'>OR</span> run this command again by specifying the absolute path for your preferred location.",
                    'required',
                    'underline'
                );
            }
        }

        if (Configuration::checkIfConfigurationHasBeenPublished()) {
            $question = 'Your already published the Tallify files. Would you like to do it again? [y/N]';
            $answer = Question::confirm($question, $this, $input, $output);

            if ($answer == false) {
                return Output::italicSingle(
                    "Process canceled. Your tallify configuration file has <span class='font-bold underline'>NOT</span> been ovewritten.",
                    'warning',
                );
            }

            Configuration::removePublishedFiles();
        }

        Configuration::publishFiles($path ?: getcwd(), $this, $input, $output, $force);

        return Output::italicSingle(
            "Tallify configuration and default files have been published to $path/tallify.",
            'success',
        );
    })->descriptions('Publish the tallify configuration and files for personal customization.', [
        'path' => 'Hard path to where you want to publish default configuration files for customisation.',
    ]);

    /**
     * Reset Tallify configuration files to their default state.
     */
    $app->command('config:reset', function (InputInterface $input, OutputInterface $output) {
        $question = 'Your current config file is about to be replaced and its content will be lost. Are you sure you want to update it? [y/N]';
        $answer = Question::confirm($question, $this, $input, $output);

        if ($answer == false) {
            return Output::italicSingle(
                "Process canceled. Your tallify configuration file has <span class='font-bold underline'>NOT</span> been updated.",
                'warning',
            );
        }

        Configuration::resetConfigurationFile($this, $input,  $output);

        return Output::italicSingle(
            "Tallify configuration file has been reset successfully!",
            'success',
        );
    })->descriptions('Reset the tallify configuration file to its default state.');

    /**
     * Add a composer or npm package to the list of default packages you want to uninstall from the default Laravel application.
     */
    $app->command('config:add package-name [--composer] [--npm] [--dev]', function (
        $packageName,
        $composer = null,
        $npm = null,
        $dev = null,
    ) {
        if (!$composer && !$npm) {
            return Output::italicSingle(
                "Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify config:add --help`</span> to see the list of available arguments.",
                'error',
            );
        }

        $libraryConfigKey = Configuration::getLibraryConfigKeyAttribute($composer ? 'composer' : 'npm', $dev, true);

        if (Configuration::checkIfPackageIsInConfigurationFile($libraryConfigKey, $packageName)) {
            return Output::italicSingle(
                "$packageName already is in your tallify configuration file at $libraryConfigKey.",
                'warning',
            );
        }

        Configuration::addPackageTo($libraryConfigKey, $packageName);

        return Output::italicSingle(
            "$packageName has been successfully added to your tallify $libraryConfigKey packages to remove from the default Laravel application.",
            'success',
        );
    })->descriptions('Add packages you want removed from the Laravel application packages.', [
        'package-name'      => 'Name of the package you would like to add to the list of the Laravel application default packages you want removed.',
        '--composer'        => 'Tells wether it is a composer package.',
        '--npm'             => 'Tells wether it is a npm package.',
        '--dev'             => 'Tells wether the package is for development only.',
    ]);

    /**
     * List all packages to remove from the default Laravel application.
     */
    $app->command('config:remove package-name [--composer] [--npm] [--dev]', function (
        $packageName,
        $composer = null,
        $npm = null,
        $dev = null,
    ) {
        if (!$composer && !$npm) {
            return Output::italicSingle(
                "Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify config:add --help`</span> to see the list of available arguments.",
                'error',
            );
        }

        $libraryConfigKey = Configuration::getLibraryConfigKeyAttribute($composer ? 'composer' : 'npm', $dev, true);

        if (!Configuration::checkIfPackageIsInConfigurationFile($libraryConfigKey, $packageName)) {
            return Output::italicSingle(
                "$packageName isn't in your tallify configuration file at $libraryConfigKey.",
                'warning',
            );
        }

        Configuration::removePackageFrom($libraryConfigKey, $packageName);

        return Output::italicSingle(
            "$packageName has been successfully remove to your tallify $libraryConfigKey packages to remove from the default Laravel application.",
            'success',
        );
    })->descriptions('Remove packages you want removed from the Laravel application packages.', [
        'package-name'      => 'Name of the package you would like to add to the list of the Laravel application default packages you want removed.',
        '--composer'        => 'Tells wether it is a composer package.',
        '--npm'             => 'Tells wether it is a npm package.',
        '--dev'             => 'Tells wether the package is for development only.',
    ]);

    /**
     * List all default packages to add to the Laravel default application.
     */
    $app->command('config:list [--composer] [--npm] [--dev]', function (
        $composer = null,
        $npm = null,
        $dev = null,
    ) {
        if (!$composer && !$npm) {
            return Output::italicSingle(
                "Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify config:list --help</span> to see the list of available arguments.",
                'error',
            );
        }

        $libraryConfigKey = Configuration::getLibraryConfigKeyAttribute($composer ? 'composer' : 'npm', $dev, true);

        $packages = Configuration::displayDefaultPackagesFor($libraryConfigKey);

        if (empty($packages)) {
            return Output::italicSingle(
                "It seems like you have no default packages at $libraryConfigKey.",
                'warning'
            );
        }

        $string = "<div class='font-bold underline'>Your $libraryConfigKey default packages:</div>";

        foreach ($packages as $index => $package) {
            $string .= "<div class='font-bold text-lime-500'><span class='text-sky-500'>" . $index . "</span><span class='px-1 text-orange-500'>=></span>" . $package . "</div>";
        }

        return Output::singleNoLevel($string);
    })->descriptions('List all packages.', [
        '--composer'        => 'List all composer packages to be removed from the Laravel default composer packages.',
        '--npm'             => 'List all npm packages to be removed from the Laravel default npm packages.',
        '--dev'             => 'List all composer development packages to be removed from the Laravel default composer development packages.',
    ]);

    /**
     * Add a composer or npm package to the list of default packages to install.
     */
    $app->command('package:add package-name [--composer] [--npm] [--dev]', function (
        $packageName,
        $composer = null,
        $npm = null,
        $dev = null,

    ) {
        if (!$composer && !$npm) {
            return Output::italicSingle(
                "Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify package:add --help`</span> to see the list of available arguments.",
                'error',
            );
        }

        $libraryConfigKey = Configuration::getLibraryConfigKeyAttribute($composer ? 'composer' : 'npm', $dev);

        if (Configuration::checkIfPackageIsInConfigurationFile($libraryConfigKey, $packageName)) {
            return Output::italicSingle(
                "$packageName already is in your tallify configuration file at $libraryConfigKey.",
                'warning',
            );
        }

        if (!Configuration::checkIfPackageExists($libraryConfigKey, $packageName)) {
            return Output::italicSingle(
                "The $packageName package does not seem to exist. <span class='font-bold underline'>Please, verify the package you want to add exists OR ensure there is no typo then try again.</span>",
                'error',
            );
        }

        Configuration::addPackageTo($libraryConfigKey, $packageName);

        return Output::italicSingle(
            "$packageName has been successfully added to your default tallify $libraryConfigKey packages.",
            'success',
        );
    })->descriptions('Add custom packages to your tallify configuration file.', [
        'package-name'      => 'Name of the package you would like to add to your tallify config.',
        '--composer'        => 'Add a composer package to your tallify config.',
        '--npm'             => 'Add a npm package to your tallify config.',
        '--dev'             => 'Tells wether the package is for development only.',
    ]);

    /**
     * Remove a composer or npm package to the list of default packages to install.
     */
    $app->command('package:remove package-name [--composer] [--npm] [--dev]', function (
        $packageName,
        $composer = null,
        $npm = null,
        $dev = null,
    ) {
        if (!$composer && !$npm) {
            return Output::italicSingle(
                "Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify package:remove --help</span> to see the list of available arguments.",
                'error',
            );
        }

        $libraryConfigKey = Configuration::getLibraryConfigKeyAttribute($composer ? 'composer' : 'npm', $dev);

        if (!Configuration::checkIfPackageIsInConfigurationFile($libraryConfigKey, $packageName)) {
            return Output::italicSingle(
                "$packageName isn't in your tallify configuration file at $libraryConfigKey.",
                'warning',
            );
        }

        Configuration::removePackageFrom($libraryConfigKey, $packageName);

        return Output::italicSingle(
            "$packageName has been successfully removed to your default tallify $libraryConfigKey packages.",
            'success',
        );
    })->descriptions('Remove custom packages to your tallify configuration file.', [
        'package-name'      => 'Name of the package you would like to add to your tallify config.',
        '--composer'        => 'Remove a composer package to your tallify config.',
        '--npm'             => 'Remove a npm package to your tallify config.',
        '--dev'             => 'Tells wether the package is for development only.',
    ]);

    /**
     * List all default packages to add to the Laravel default application.
     */
    $app->command('package:list [--composer] [--npm] [--dev]', function (
        $composer = null,
        $npm = null,
        $dev = null,
    ) {
        if (!$composer && !$npm) {
            return Output::italicSingle(
                "Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify package:list --help</span> to see the list of available arguments.",
                'error',
            );
        }

        $libraryConfigKey = Configuration::getLibraryConfigKeyAttribute($composer ? 'composer' : 'npm', $dev);

        $packages = Configuration::displayDefaultPackagesFor($libraryConfigKey);

        if (empty($packages)) {
            return Output::italicSingle(
                "It seems like you have no default packages at $libraryConfigKey.",
                'warning'
            );
        }

        $string = "<div class='font-bold underline'>Your $libraryConfigKey default packages:</div>";

        foreach ($packages as $index => $package) {
            $string .= "<div class='font-bold text-lime-500'><span class='text-sky-500'>" . $index . "</span><span class='px-1 text-orange-500'>=></span>" . $package . "</div>";
        }

        return Output::singleNoLevel($string);
    })->descriptions('List all packages.', [
        '--composer'        => 'List all composer default packages.',
        '--npm'             => 'List all npm default packages.',
        '--dev'             => 'List all composer --dev default packages.',
    ]);

    /**
     * Add a stub to the list of default stubs to install.
     */
    $app->command('stub:add stub-name stub-path [--directory]', function (
        $stubName,
        $stubPath,
        $directory = null
    ) {
        if (Configuration::checkIfStubIsInConfigurationFile($stubName, $stubPath)) {
            return Output::italicSingle(
                "$stubName already is in your tallify configuration file.",
                'warning',
            );
        }

        Configuration::addStub($stubName, $stubPath, $directory);

        return Output::italicSingle(
            "$stubName has been successfully added to your default stubs.",
            'success',
        );
    })->descriptions('Add custom stubs to your tallify configuration file.', [
        'stub-name'       => 'Name of the stub file you would like to add to your tallify config.',
        'stub-path'       => 'Path withing your Laravel application you want this stub to be copied.',
        '--directory'     => 'Tell if the added stub is a directory.',
    ]);

    /**
     * Remove a stub to the list of default stubs to install.
     */
    $app->command('stub:remove stub-name [--directory]', function ($stubName, $directory) {
        if (!Configuration::checkIfStubIsInConfigurationFile($stubName)) {
            return Output::italicSingle(
                "It looks like $stubName was <span class='font-bold underline'>NOT</span> in your tallify configuration file.",
                'warning',
            );
        }

        Configuration::removeStub($stubName, $directory);

        return Output::italicSingle(
            "$stubName has been successfully removed to your default stubs.",
            'success',
        );
    })->descriptions('Remove custom stubs to your tallify configuration file.', [
        'stub-name'       => 'Name of the stub file you would like to remove to your tallify config.',
        '--directory'     => 'Tell if the removed stub is a directory.',
    ]);

    /**
     * List all default stubs.
     */
    $app->command('stubs:list [--directory]', function ($directory) {
        $stubs = Configuration::displayDefaultStubs($directory);

        if (empty($stubs)) {
            return Output::italicSingle(
                "It seems like you have no default stubs.",
                'warning',
            );
        }

        $string = "<div class='font-bold underline'>Your default stubs: (<em class='font-bold'>'Stub filename'</span><span class='px-1'>=></span>'path/to/install/file'</em>)</div>";

        foreach ($stubs as $index => $stub) {
            $string .= "<div class='font-bold text-lime-500'><span class='text-sky-500'>'" . $index . "'</span><span class='px-1 text-orange-500'>=></span>'" . $stub . "'</div>";
        }

        return Output::singleNoLevel($string);
    })->descriptions('List all stubs.', [
        "--directory" => "List all default stubs directories."
    ]);



    if (Configuration::checkIfParked()) {
        /**
         * Command to "tallify" a laravel project
         */
        $app->command('build project-name [--force]', function (
            $projectName,
            InputInterface $input,
            OutputInterface $output,
            $force = null
        ) {
            // verify application exists
            if (!Build::checkApplicationExists($projectName)) {
                return Output::italicSingle(
                    "It look like $projectName is not a laravel project. Make sure you have the correct spelling and try again.",
                    'error',
                );
            }

            // verify all stubs exists
            if (!Build::checkAllStubsExists()) {
                return Output::italicSingle(
                    "It look like some of your default stubs are missing. If the problem persist, try resetting your configuration file and try again.",
                    'error',
                );
            }

            $output->write(PHP_EOL . TALLIFY_ASCII . PHP_EOL . PHP_EOL);

            sleep(1);

            if (!$force) {
                if (Build::checkIfProjectHasBeenTallifiedAlready($projectName)) {
                    $answer = Build::askUserPermissionToOverride($this, $input, $output);

                    if ($answer == false) {
                        return Output::italicSingle(
                            "Process canceled. The project was <span class='font-bold underline'>NOT</span> 'tallified'.",
                            'warning'
                        );
                    }
                }
            }

            // copy all stubs
            Build::copyStubsToLaravelProject($projectName);

            // install packages
            Build::installComposerPackages($projectName);
            Build::uninstallComposerPackages($projectName);
            Build::installComposerDevelopmentPackages($projectName);
            Build::uninstallComposerDevelopmentPackages($projectName);
            Build::addNpmPackages($projectName);
            Build::removeNpmPackages($projectName);
            Build::runArtisanCommands($projectName);

            // add files to git ignore

        })->descriptions('Tallify a given Laravel application.', [
            'project-name' => 'Tell Tallify what Laravel project you would like to "tallify"',
            '--force' => 'Force the "tallification" of a already "tallified" Laravel project',
        ]);
    }
}

return $app;
