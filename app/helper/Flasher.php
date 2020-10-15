<?php

namespace app\helper;
/**
 * Class Flasher
 *
 * Class ini menangani flash data
 *
 * @author Hachidaime
 */
class Flasher
{
    /**
     * function setFlash
     *
     * Fungsi ini menangani set flash data
     *
     * @param string $message pesan flash data
     * @param string $module nama module
     * @param string $type tipe pesan: success|error|warning|info|question
     */
    public static function setFlash(
        string $message,
        string $module,
        string $type
    ) {
        $_SESSION['flash'] = [
            'message' => $message,
            'module' => $module,
            'type' => $type,
        ];
    }

    /**
     * function getFlash
     *
     * Fungsi ini menangani mendapatkan flash data
     *
     * @return array ['message' => $message, 'module' => $module, 'type' => type]
     */
    public static function getFlash()
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
    }
}
