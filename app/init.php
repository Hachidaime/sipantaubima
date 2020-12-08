<?php
use app\core\App;

/**
 * Menyertakan konfigurasi Local
 */
require_once 'config/Local.php';

/**
 * Menyertakan konfigurasi Global
 */
require_once 'config/Config.php';

/**
 * Menyertakan Composer Autoloading
 */
require_once 'vendor/autoload.php';

/**
 * Instansiasi AltoRouter
 */
$router = new AltoRouter();
include 'config/Routes.php';

/**
 * Instansiasi Smarty Template Engine
 */
$smarty = new Smarty();
$smarty->compile_check = true;
$smarty
    ->setTemplateDir(DOC_ROOT . 'app/views') // ? \Templates directory
    ->setCompileDir(DOC_ROOT . 'app/views_c'); // ? Templates cache directory

/**
 * Menyertakan class DB
 */
require_once 'libs/DB.php';

/**
 * Instansiasi Marei Morsy DB
 */
$db = DB::getInstance();

/**
 * Instansiasi App
 */
$app = new App();
