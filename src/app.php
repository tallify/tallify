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
$version = '0.1.19';

$app = new Application('Tallify installer', $version);

/**
 * Install Tallify and any required services.
 */
$app->command('install', function () {
    sleep(1);

    Configuration::install();

    Output::italicSingle(
        "Tallify was successfully installed.",
        'success'
    );

    sleep(1);

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

        sleep(1);

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
     * Returns the path to the custom tallify configuration files.
     */
    $app->command('published', function () {
        $publishedPath = Configuration::getPublishedPath();

        if (empty($publishedPath)) {
            $path = TALLIFY_HOME_PATH;

            return Output::italicSingle(
                "<span class='font-bold underline'>$path</span> is where your tallify configuration file lives.",
                'info',
            );
        }

        return Output::italicSingle(
            "<span class='font-bold underline'>$publishedPath</span> is where your custom tallify configuration file lives.",
            'info',
        );
    })->descriptions('Shows the directory where your custom tallify configuration file lives.');

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
    $app->command('detach:add package-name [--composer] [--npm] [--dev]', function (
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
    $app->command('detach:remove package-name [--composer] [--npm] [--dev]', function (
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
    $app->command('detach:list [--composer] [--npm] [--dev]', function (
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
    })->descriptions('List all packages you want to remove from the default Laravel Application.', [
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
    })->descriptions('List all packages you want to add to the default Laravel Application packages.', [
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
            "$stubName has been successfully removed from your default stubs.",
            'success',
        );
    })->descriptions('Remove custom stubs to your tallify configuration file.', [
        'stub-name'       => 'Name of the stub file you would like to remove to your tallify config.',
        '--directory'     => 'Tell if the removed stub is a directory.',
    ]);

    /**
     * List all default stubs.
     */
    $app->command('stub:list [--directory]', function ($directory) {
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

    /**
     * Add an artisan command to the list of default artisan commands to run post install.
     */
    $app->command('command:add command-name [--post-update]', function ($commandName, $postUpdate = null) {
        if (Configuration::checkIfArtisanCommandIsInConfigurationFile($commandName, $postUpdate)) {
            return Output::italicSingle(
                "'$commandName' already is in your tallify configuration file.",
                'warning',
            );
        }

        Configuration::addArtisanCommand($commandName, $postUpdate);

        return Output::italicSingle(
            "'$commandName' has been successfully added to your default artisan commands to run post install/update.",
            'success',
        );
    })->descriptions('Add custom artisan command to run post install to your tallify configuration file.', [
        'command-name'           => 'Command to be added to the list of artisan commands to run post install.',
        '--post-update'     => 'Option to tell Tallify that the command is a post-update-cmd artisan command.',
    ]);

    /**
     * Remove an artisan command to the list of default artisan commands to run post install.
     */
    $app->command('command:remove command-name [--post-update]', function ($commandName, $postUpdate = null) {
        if (!Configuration::checkIfArtisanCommandIsInConfigurationFile($commandName, $postUpdate)) {
            return Output::italicSingle(
                "It looks like '$commandName' was <span class='font-bold underline'>NOT</span> in your tallify configuration file.",
                'warning',
            );
        }

        Configuration::removeArtisanCommand($commandName, $postUpdate);

        return Output::italicSingle(
            "'$commandName' has been successfully removed from your default artisan commands to run post install/update.",
            'success',
        );
    })->descriptions('Remove custom artisan command to run post install to your tallify configuration file.', [
        'command-name'       => 'Command to be removed from the list of artisan commands to run post install.',
        '--post-update'     => 'Option to tell Tallify that the command is a post-update-cmd artisan command.',
    ]);

    /**
     * List all artisan commands from the Tallify configuration file.
     */
    $app->command('command:list [--post-update]', function ($postUpdate = null) {
        $commands = Configuration::displayDefaultArtisanCommands($postUpdate);

        if (empty($commands)) {
            return Output::italicSingle(
                "It seems like you have no default post install/update artisan commands.",
                'warning',
            );
        }

        $string = "<div class='font-bold underline'>Your post install/update commands:</div>";

        foreach ($commands as $index => $command) {
            $string .= "<div class='font-bold text-lime-500'><span class='text-sky-500'>'" . $index . "'</span><span class='px-1 text-orange-500'>=></span>'" . $command . "'</div>";
        }

        return Output::singleNoLevel($string);
    })->descriptions('List all post install/update artisan commands from the Tallify configuration file.', [
        '--post-update'     => 'Option to list all post-update-cmd artisan command.',
    ]);

    /**
     * Add an environment variable to the .env file
     */
    $app->command('env:add env-variable', function ($envVariable) {
        if (Configuration::checkIfVariableIsInConfigurationFile($envVariable)) {
            return Output::italicSingle(
                "$envVariable already is in your tallify configuration file.",
                'warning',
            );
        }

        Configuration::addEnvVariable($envVariable);

        return Output::italicSingle(
            "$envVariable has been successfully added to the list of files you want to add to the .env file.",
            'success',
        );
    })->descriptions('Add custom files to your env file.', [
        'env-variable'           => 'Environment variable you want to add to your .env file',
    ]);

    /**
     * Remove an environment variable to the .env file
     */
    $app->command('env:remove env-variable', function ($envVariable) {
        if (!Configuration::checkIfVariableIsInConfigurationFile($envVariable)) {
            return Output::italicSingle(
                "It looks like $envVariable was <span class='font-bold underline'>NOT</span> in your tallify configuration file.",
                'warning',
            );
        }

        Configuration::removeEnvVariable($envVariable);

        return Output::italicSingle(
            "$envVariable has been successfully removed from the list of environment variables you want to add to the .env file.",
            'success',
        );
    })->descriptions('Remove an environment variable from your tallify configuration file.', [
        'env-variable'       => 'Environment variable you want to remove from the environment variable list in your .env file',
    ]);

    /**
     * List all environment variables from the Tallify configuration file.
     */
    $app->command('env:list', function () {
        $variables = Configuration::displayDefaultEnvironmentVariables();

        if (empty($variables)) {
            return Output::italicSingle(
                "It seems like you have no default environment variables.",
                'warning',
            );
        }

        $string = "<div class='font-bold underline'>Your environment variables to add to the .env file:</div>";

        foreach ($variables as $index => $variable) {
            $string .= "<div class='font-bold text-lime-500'><span class='text-sky-500'>'" . $index . "'</span><span class='px-1 text-orange-500'>=></span>'" . $variable . "'</div>";
        }

        return Output::singleNoLevel($string);
    })->descriptions('List all environment variables from the Tallify configuration file.');

    /**
     * Add an file to the .gitignore file
     */
    $app->command('gitignore:add file-path', function ($filePath) {
        if (Configuration::checkIfFileIsInConfigurationFile($filePath)) {
            return Output::italicSingle(
                "$filePath already is in your tallify configuration file.",
                'warning',
            );
        }

        Configuration::addFileToGitignore($filePath);

        return Output::italicSingle(
            "$filePath has been successfully added to the list of files you want to add to the .gitignore file.",
            'success',
        );
    })->descriptions('Add custom files to your .gitignore file.', [
        'file-path'           => 'Path of the file you want to add to your .gitignore file',
    ]);

    /**
     * Remove a file from the .gitignore file
     */
    $app->command('gitignore:remove file-path', function ($filePath) {
        if (!Configuration::checkIfFileIsInConfigurationFile($filePath)) {
            return Output::italicSingle(
                "It looks like $filePath was <span class='font-bold underline'>NOT</span> in your tallify configuration file.",
                'warning',
            );
        }

        Configuration::removeFileFromGitignore($filePath);

        return Output::italicSingle(
            "$filePath has been successfully removed from the list of files you want to add to the .gitignore file.",
            'success',
        );
    })->descriptions('Remove file from the list of files you want to add to the .gitignore file.', [
        'file-path'       => 'File path you want to remove from the files you want to add in the .gitignore file',
    ]);

    /**
     * List all .env files from the Tallify configuration file.
     */
    $app->command('gitignore:list', function () {
        $variables = Configuration::displayDefaultGitignoreFiles();

        if (empty($variables)) {
            return Output::italicSingle(
                "It seems like you have no default .gitignore files.",
                'warning',
            );
        }

        $string = "<div class='font-bold underline'>Your files to add to the .gitignore file:</div>";

        foreach ($variables as $index => $variable) {
            $string .= "<div class='font-bold text-lime-500'><span class='text-sky-500'>'" . $index . "'</span><span class='px-1 text-orange-500'>=></span>'" . $variable . "'</div>";
        }

        return Output::singleNoLevel($string);
    })->descriptions('List all the .env files from the Tallify configuration file.');




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

            // add post-update-cmd
            Build::addPostUpdateArtisanCommands($projectName);

            // install packages
            Build::installComposerPackages($projectName);
            Build::installComposerDevelopmentPackages($projectName);
            Build::uninstallComposerPackages($projectName);
            Build::uninstallComposerDevelopmentPackages($projectName);
            Build::addNpmPackages($projectName);
            Build::removeNpmPackages($projectName);

            // run artisan commands
            Build::runArtisanCommands($projectName);

            // secure application
            Build::secureAppUrlInDotEnv($projectName);

            // add environment variables to .env
            Build::addFilesToDotEnv($projectName);

            // add files to git ignore
            Build::addFilesToGitignore($projectName);

            // valet secure
            Build::valetSecure($projectName);
        })->descriptions('Tallify a given Laravel application.', [
            'project-name' => 'Tell Tallify what Laravel project you would like to "tallify"',
            '--force' => 'Force the "tallification" of a already "tallified" Laravel project',
        ]);
    }
}

return $app;
