<?php

/**
 * Not sofisticated, just straight forward
 *
 * @return bool
 */
function isLocal() {
    $srv = filter_input(INPUT_SERVER, 'SERVER_ADDR');
    return strpos($srv, '192.168') !== false || strpos($srv, '10.') === 0 || $srv == 'localhost' ||
        $srv == '127.0.0.1' || $srv == '::1';
}

isLocal() ?
    define('BASEDIR', '/2clics/public') : define('BASEDIR', '');


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH),
    realpath(APPLICATION_PATH . '/modules'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();