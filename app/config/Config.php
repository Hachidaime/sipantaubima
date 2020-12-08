<?php

/**
 * Controller default
 *
 * @var string
 */
define(
    'DEFAULT_CONTROLLER',
    !empty($_SESSION['USER']['id']) ? 'DashboardController' : 'HomeController'
);

/**
 * Method default
 *
 * @var string
 */
define('DEFAULT_METHOD', 'index');

/**
 * Name default
 *
 * @var string
 */
define('DEFAULT_NAME', 'dashboard');

/**
 * Jumlah baris yang ditampilkan per halaman pada pagination
 *
 * @var integer
 */
define('ROWS_PER_PAGE', 10);

/**
 * Jumlah halaman yang ditampilkan
 * sebelum dan sesudah halaman aktif
 * pada pagination
 *
 * @var integer
 */
define('SURROUND_COUNT', 1);

/**
 * Format Tanggal Waktu yang ditampilkan pada view
 *
 * @var string
 * @see https://www.smarty.net/docs/en/language.modifier.date.format.tpl
 */
define('DATETIME_FORMAT', '%d/%m/%Y %H.%M.%S');

/**
 * Format Tanggal yang ditampilkan pada view
 *
 * @var string
 * @see https://www.smarty.net/docs/en/language.modifier.date.format.tpl
 */
define('DATE_FORMAT', '%d/%m/%Y');

/**
 * Format Waktu yang ditampilkan pada view
 *
 * @var string
 * @see https://www.smarty.net/docs/en/language.modifier.date.format.tpl
 */
define('TIME_FORMAT', '%H:%i:%s');

define('MY_KEY', 'everybodyjump');

/**
 * Pilihan Sumber Dana
 *
 * @var array
 */
define('SOF_OPT', [
    1 => 'APBD',
    2 => 'APBDP',
    3 => 'DAK',
    4 => 'BANPROV'
]);

/* Main Header Image */
define('MAIN_HEADER', BASE_URL . '/assets/img/main-header.jpg'); // ? 16:3

/* Home Images */
define('IMG_LINK1', BASE_URL . '/assets/img/image-link1.png'); // ? 1:1
define('IMG_LINK2', BASE_URL . '/assets/img/image-link2.png'); // ? 1:1
define('IMG_LINK3', BASE_URL . '/assets/img/image-link3.png'); // ? 1:1

define('IMG_SLIDE1', BASE_URL . '/assets/img/image-slide1.png'); // ? 16:5
define('IMG_SLIDE2', BASE_URL . '/assets/img/image-slide2.png'); // ? 16:5
define('IMG_SLIDE3', BASE_URL . '/assets/img/image-slide3.png'); // ? 16:5

/* Login Background Image */
define('LOGIN_BG', BASE_URL . '/assets/img/bg-main.png');
