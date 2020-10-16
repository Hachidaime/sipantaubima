<?php

/**
 * Cek session_id
 *
 * Memulai session jika tidak ada session_id
 */
if (!session_id()) {
    session_start();
}

header('Access-Control-Allow-Origin: *');

/**
 * Menampilkan error
 *
 * H
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

/**
 * Menyertakan file init.php
 */
require_once 'app/init.php';
