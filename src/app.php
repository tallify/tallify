<?php

use Silly\Application;
use function Termwind\{render};
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
$version = '0.1.8';

$app = new Application('Tallify installer', $version);

/**
 * Install Tallify and any required services.
 */
$app->command('install', function (InputInterface $input, OutputInterface $output) {
    Configuration::install();

    render("
        <div class='py-1'>
            <div class='mb-1'>
                <div class='px-1 text-white bg-green-600'>SUCCESS!</div>
                <em class='ml-1'>
                    Tallify was successfully installed.
                </em>
            </div>
            <div>
                <div class='px-1 font-bold text-white bg-purple-400'>Action required</div>
                <em class='ml-1'>Please, go to the folder where you keep all of your Laravel projects and use the <span class='px-1 font-bold text-white bg-gray-800'>tallify park</span> command.</em>
            </div>
        </div>
    ");
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
        $answer = Question::confirm($this, $question, $input, $output);

        if ($answer == false) {
            return render("
                <div class='mb-1'>
                    <div class='px-1 font-bold text-white bg-orange-600'>WARNING !</div>
                    <em class='ml-1'>Process canceled. Tallify services have <span class='font-bold underline'>NOT</span> been uninstalled.</em>
                </div>
            ");
        }

        Configuration::uninstall();

        render("
            <div class='flex mb-1'>
                <div class='px-1 text-white bg-green-600'>SUCCESS!</div>
                <em class='ml-1'>
                    Tallify was successfully installed.
                </em>
            </div>
        ");
    })->descriptions('Uninstall the Tallify services');

    /**
     * Publish Tallify configuration and files to a given path.
     */
    $app->command('config:publish [path]', function (InputInterface $input, OutputInterface $output, $path = null) {
        if (!$path) {
            $helper = $this->getHelperSet()->get('question');
            $question = new ConfirmationQuestion(
                'Is ' . getcwd() . ' the folder where you develop all your laravel apps? [y/N]',
                false
            );

            if (false === $helper->ask($input, $output, $question)) {
                return render("
                    <div class='mb-1'>
                        <div class='px-1 mb-1 font-bold bg-purple-600'>ACTION REQUIRED !</div>
                        <div><em class='ml-1 underline'>Go to your preferred location for the published files <span class='font-bold'>OR</span> run this command again by specifying the absolute path for your preferred location.</em></div>
                    </div>
                ");
            }
        }

        Configuration::publishFiles($path ?: getcwd());

        render("
            <div class='flex mb-1'>
                <div class='px-1 text-white bg-green-600'>SUCCESS!</div>
                <em class='ml-1'>
                    Tallify configuration and default files have been published.
                </em>
            </div>
        ");
    })->descriptions('Publish the tallify configuration and files for personal customization.', [
        'path' => 'Hard path to where you want to publish default configuration files for customisation.',
    ]);

    /**
     * Reset Tallify configuration files to their default state.
     */
    $app->command('config:reset', function (InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion(
            "Your current config file is about to be replaced and its content will be lost. Are you sure you want to update it? [y/N]",
            false
        );

        if (false === $helper->ask($input, $output, $question)) {
            return render("
                <div class='mb-1'>
                    <div class='px-1 font-bold text-white bg-orange-600'>WARNING !</div>
                    <em class='ml-1'>Process canceled. Your tallify configuration file has <span class='font-bold underline'>NOT</span> been updated.</em>
                </div>
            ");
        }

        Configuration::resetConfigurationFile();

        return render("
            <div class='flex mb-1'>
                <div class='px-1 text-white bg-green-600'>Success!</div>
                <em class='ml-1'>
                    Tallify configuration file has been updated successfully!
                </em>
            </div>
        ");
    })->descriptions('Reset the tallify configuration file to its default state.');

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
            return render("
                <div class='flex mb-1'>
                    <div class='px-1 text-white bg-red-600'>ERROR!</div>
                    <em class='ml-1'>
                        Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify config:add --help`</span> to see the list of available arguments.
                    </em>
                </div>
            ");
        }

        $library = $composer ? 'composer' : 'npm';

        if (Configuration::checkIfPackageIsInConfigurationFile($library, $packageName, $dev ? '--dev' : '')) {
            return render("
                <div class='mb-1'>
                    <div class='px-1 text-white bg-orange-600'>WARNING!</div>
                    <em class='ml-1'>
                        $packageName already is in your tallify configuration file.
                    </em>
                </div>
            ");
        }

        if (!Configuration::checkIfPackageExists($library, $packageName)) {
            return render("
                <div class='mb-1'>
                    <div class='px-1 text-white bg-red-600'>ERROR!</div>
                    <em class='ml-1'>
                        The $packageName $library package does not seem to exist. <span class='font-bold underline'>Please, verify the package you want to add exists OR ensure there is no typo then try again.</span>
                    </em>
                </div>
            ");
        }

        Configuration::addPackageTo($library, $packageName, $dev ? '--dev' : '');

        return render("
            <div class='mb-1'>
                <div class='px-1 text-white bg-green-600'>SUCCESS!</div>
                <em class='ml-1'>
                    $packageName has been successfully added to your default tallify packages.
                </em>
            </div>
        ");
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
            return render("
                <div class='flex mb-1'>
                    <div class='px-1 text-white bg-red-600'>ERROR!</div>
                    <em class='ml-1'>
                        Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify config:remove --help</span> to see the list of available arguments.
                    </em>
                </div>
            ");
        }

        $library = $composer ? 'composer' : 'npm';

        $packageRemoved = Configuration::removePackageFrom($library, $packageName, $dev ? '--dev' : '');

        if (!$packageRemoved) {
            return render("
                <div class='mb-1'>
                    <div class='px-1 text-white bg-orange-600'>WARNING!</div>
                    <em class='ml-1'>
                        It looks like $packageName was <span class='font-bold underline'>NOT</span> in your tallify configuration file.
                    </em>
                </div>
            ");
        }

        return render("
            <div class='mb-1'>
                <div class='px-1 text-white bg-green-600'>SUCCESS!</div>
                <em class='ml-1'>
                    $packageName has been successfully removed from your default tallify packages.
                </em>
            </div>
        ");
    })->descriptions('Remove custom packages to your tallify configuration file.', [
        'package-name'      => 'Name of the package you would like to add to your tallify config.',
        '--composer'        => 'Remove a composer package to your tallify config.',
        '--npm'             => 'Remove a npm package to your tallify config.',
        '--dev'             => 'Tells wether the package is for development only.',
    ]);

    /**
     * List all default packages.
     */
    $app->command('package:list [--composer] [--npm] [--dev]', function (
        $composer = null,
        $npm = null,
        $dev = null,
    ) {
        if (!$composer && !$npm) {
            return render("
                <div class='flex mb-1'>
                    <div class='px-1 text-white bg-red-600'>ERROR!</div>
                    <em class='ml-1'>
                        Missing argument. Use the <span class='px-1 font-bold text-white bg-gray-800'>tallify list:packages --help</span> to see the list of available arguments.
                    </em>
                </div>
            ");
        }

        $library = $composer ? 'composer' : 'npm';

        $packages = Configuration::displayDefaultPackagesFor($library, $dev ? '--dev' : '');

        if (empty($packages)) {
            return render("
                <div class='mb-1'>
                    <div class='px-1 text-white bg-orange-600'>WARNING!</div>
                    <em class='ml-1'>
                        It seems like you have no default $library packages.
                    </em>
                </div>
            ");
        }

        $libraryForHumans = $npm ? 'npm' : ($dev ? 'composer --dev' : 'composer');

        $string = "";

        foreach ($packages as $index => $package) {
            $string .= "<div class='font-bold text-lime-500'><span class='text-sky-500'>" . $index . "</span><span class='px-1 text-orange-500'>=></span>" . $package . "</div>";
        }

        return render("
            <div class='mb-1'>
                <div class='font-bold underline'>Your $libraryForHumans default packages:</div>
                $string
            </div>
        ");
    })->descriptions('List all packages.', [
        '--composer'        => 'List all composer default packages.',
        '--npm'             => 'List all npm default packages.',
        '--dev'             => 'List all composer --dev default packages.',
    ]);

    /**
     * List all default stubs.
     */
    $app->command('stubs:list', function () {
        $stubs = Configuration::displayDefaultStubs();

        if (empty($stubs)) {
            return render("
                <div class='mb-1'>
                    <div class='px-1 text-white bg-orange-600'>WARNING!</div>
                    <em class='ml-1'>
                        It seems like you have no default stubs.
                    </em>
                </div>
            ");
        }

        $string = "";

        foreach ($stubs as $index => $stub) {
            $string .= "<div class='font-bold text-lime-500'><span class='text-sky-500'>'" . $index . "'</span><span class='px-1 text-orange-500'>=></span>'" . $stub . "'</div>";
        }

        return render("
            <div class='mb-1'>
                <div class='font-bold underline'>Your default stubs: (<em class='font-bold'>'Stub filename'</span><span class='px-1'>=></span>'path/to/install/file'</em>)</div>
                $string
            </div>
        ");
    })->descriptions('List all stubs.');

    /**
     * Tell Tallify where all your Laravel applications live.
     */
    $app->command('park [path]', function ($path = null) {
        Configuration::setParkedPath($path ?: getcwd());

        render("
            <div class='flex'>
                <div class='px-1 text-white bg-green-600'>SUCCESS!</div>
                <em class='ml-1'>
                    Tallify is ready to work its magic!
                </em>
            </div>
        ");
    })->descriptions('Tells tallify where all your Laravel applications lives to be able to "Tallify" them.');

    /**
     * Returns the path to the user folder where all Laravel applications live.
     */
    $app->command('parked', function () {
        $parkedPath = Configuration::getParkedPath();

        if (empty($parkedPath)) {
            return render("
                <div class='flex mb-1'>
                    <div class='px-1 text-white bg-orange-600'>WARNING!</div>
                    <em class='ml-1'>
                        It looks like you did not set the path to your Laravel applications.
                    </em>
                </div>
            ");
        }

        render("
            <div class='flex mb-1'>
                <div class='px-1 bg-blue-600'>INFO!</div>
                <em class='ml-1'>
                    <span class='font-bold underline'>$parkedPath</span> is where your laravel applications live.
                </em>
            </div>
        ");
    })->descriptions('Shows the directory where all your Laravel applivations live.');

    if (Configuration::checkIfParked()) {
        /**
         * Command to "tallify" a laravel project
         */
        $app->command('build project-name', function ($porjectName) {
        })->descriptions('Tallify a given Laravel application.');
    }
}



return $app;
