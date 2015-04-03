<?php
// Get environment & autoloader
define('ANAX_INSTALL_PATH', realpath(__DIR__ . '/../vendor/anax/mvc') . '/');
define('ANAX_APP_PATH',     realpath(__DIR__ . '/../') . '/app/');
define('NAVIGARE_INSTALL_PATH', realpath(__DIR__ . '/../') . '/');

/**
 * Include global functions.
 *
 */
include(ANAX_INSTALL_PATH . 'src/functions.php');
include __DIR__ . "/../functions.php";
include __DIR__ . "/../autoloader.php";
include __DIR__ . "/../vendor/autoload.php";
include __DIR__ . "/../vendor/anax/mvc/app/config/autoloader.php";



// Create services and inject into the app. 
$di  = new \Anax\DI\CDIFactoryDefault();
$app = new \Anax\Kernel\CAnax($di);
$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);
$app->session;

