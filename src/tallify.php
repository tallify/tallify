#!/usr/bin/env php
<?php

/**
 * Define the user's "~/.config/tallify" path.
 */
define('TALLIFY_HOME_PATH', posix_getpwuid(fileowner(__FILE__))['dir'] . '/.config/tallify');
define('TALLIFY_SRC_PATH', getcwd());
define("TALLIFY_ASCII", "  <fg=gray>
████████╗ █████╗ ██╗     ██╗     ██╗███████╗██╗   ██╗
╚══██╔══╝██╔══██╗██║     ██║     ██║██╔════╝╚██╗ ██╔╝
   ██║   ███████║██║     ██║     ██║█████╗   ╚████╔╝
   ██║   ██╔══██║██║     ██║     ██║██╔══╝    ╚██╔╝
   ██║   ██║  ██║███████╗███████╗██║██║        ██║
   ╚═╝   ╚═╝  ╚═╝╚══════╝╚══════╝╚═╝╚═╝        ╚═╝   </>");



require_once __DIR__ . '/app.php';

/**
 * Run the application.
 */
$app->run();
