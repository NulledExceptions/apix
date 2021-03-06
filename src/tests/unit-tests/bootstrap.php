<?php

/**
 *
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license     http://opensource.org/licenses/BSD-3-Clause  New BSD License
 *
 */

namespace Apix;

date_default_timezone_set('UTC');

define('DEBUG', true);
define('UNIT_TEST', true);

// define('APP_TOPDIR', realpath(__DIR__ . '/../../php'));
define('APP_TESTDIR', realpath(__DIR__ . '/php'));
define('APP_VENDOR', realpath(__DIR__ . '/../../../vendor'));

// Composer
$loader = require APP_VENDOR . '/autoload.php';
$loader->add('Apix', APP_TESTDIR);