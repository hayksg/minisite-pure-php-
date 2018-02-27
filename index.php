<?php

/*  Front controller  */

use Application\Components\Router;
use Application\Controller\ErrorController;

// Settings
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
session_start();

// Including files
define('ROOT', __DIR__ . '/');
require_once(ROOT . 'config/autoload.php');

// Catching all Exceptions
set_exception_handler([new ErrorController, 'indexAction']);

// Router's invoke
(new Router())->run();
