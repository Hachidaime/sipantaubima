<?php

namespace app\helper;

use DateTime;

/**
 * Class Functions
 *
 * Class ini akan menangani fungsi-fungsi pembantu.
 *
 * PHP VERSION 7
 *
 * @author Hachidaime
 */
class Functions
{
    /**
     * function getUserId
     *
     * Mendapatkan login usr_id
     *
     * @access public
     * @return string $_SESSION['USER']['usr_id']
     */
    public static function getUserId()
    {
        return $_SESSION['USER']['id'];
    }

    /**
     * function getNowDatetime
     *
     * Mendapatkan tanggal waktu sekarang
     *
     * @access public
     * @return string date('Y-m-d H:i:s')
     */
    public static function getNowDatetime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * function getStringBetween
     *
     * Fungsi ini mengembalikan string di antara 2 string
     *
     * Contoh : Functions::getStringBetween('Halo Dunia', 'Ha', 'nia')
     *          menghasilkan "lo Du"
     *
     * @access public
     * @param string $search nilai yang dicari
     * @param string $from nilai awal
     * @param string $to nilai akhir
     * @return string $string nilai di antara $from dan $to
     */
    public static function getStringBetween(
        string $search,
        string $from,
        string $to
    ) {
        $string = substr($search, strpos($search, $from) + strlen($from));
        if (strstr($string, $to, true) != false) {
            $string = strstr($string, $to, true);
        }
        return $string;
    }

    /**
     * function getCreated
     *
     * Fungsi ini mengembalikan created login usr_id dan tanggal waktu sekarang
     *
     * @access public
     * @return array ['created_by' => self::getUserId(), 'created_at' => self::getNowDatetime()]
     */
    public static function getCreated()
    {
        return [
            'created_by' => self::getUserId(),
            'created_at' => self::getNowDatetime(),
        ];
    }

    /**
     * function getUpdated
     *
     * Fungsi ini mengembalikan updated login usr_id dan tanggal waktu sekarang
     *
     * @access public
     * @return array ['updated_by' => self::getUserId(), 'updated_at' => self::getNowDatetime()]
     */
    public static function getUpdated()
    {
        return [
            'updated_by' => self::getUserId(),
            'updated_at' => self::getNowDatetime(),
        ];
    }

    /**
     * function getDeleted
     *
     * Fungsi ini mengembalikan deleted login usr_id dan tanggal waktu sekarang
     *
     * @access public
     * @return array ['deleted_by' => self::getUserId(), 'deleted_at' => self::getNowDatetime()]
     */
    public static function getDeleted()
    {
        return [
            'deleted_by' => self::getUserId(),
            'deleted_at' => self::getNowDatetime(),
        ];
    }

    /**
     * function getRemoteIp
     *
     * Fungsi ini mengembalikan user remote IP
     *
     * @access public
     * @param bool $is_array jenis return array atau bukan
     * @return array|string ['remote_ip' => $remote_ip]|$remote_ip
     */
    public static function getRemoteIp(bool $is_array = true)
    {
        return $is_array
            ? ['remote_ip' => $_SERVER['REMOTE_ADDR']]
            : $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @see https://stackoverflow.com/a/7729790
     */
    public static function splitCamelCase(string $string)
    {
        $re = '/(?#! splitCamelCase Rev:20140412)
    # Split camelCase "words". Two global alternatives. Either g1of2:
      (?<=[a-z])      # Position is after a lowercase,
      (?=[A-Z])       # and before an uppercase letter.
    | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
      (?=[A-Z][a-z])  # and before upper-then-lower case.
    /x';
        return preg_split($re, $string);
    }

    public static function camelize(string $string, $pascalize = false)
    {
        $str = str_replace('_', '', ucwords($string, '_'));

        if (!$pascalize) {
            $str = lcfirst($str);
        }

        return $str;
    }

    public static function slugify(string $string)
    {
        return strtolower(rawurlencode(str_replace(' ', '-', trim($string))));
    }

    public static function encrypt($data, $key = MY_KEY)
    {
        // TODO: Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // TODO: Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(
            openssl_cipher_iv_length('aes-256-cbc'),
        );
        // TODO: Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt(
            $data,
            'aes-256-cbc',
            $encryption_key,
            0,
            $iv,
        );
        // TODO: The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function decrypt($data, $key = MY_KEY)
    {
        // TODO: Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // TODO: To decrypt, split the encrypted data from our IV - our unique separator used was "::"
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt(
            $encrypted_data,
            'aes-256-cbc',
            $encryption_key,
            0,
            $iv,
        );
    }

    public static function listToOptions(
        array $array,
        string $key,
        string $value
    ) {
        return array_combine(
            array_map(function ($el) use ($array, $key) {
                return $array[$el][$key];
            }, array_keys($array)),
            array_map(function ($el) use ($array, $value) {
                return $array[$el][$value];
            }, array_keys($array)),
        );
    }

    public static function dateFormat($orig, $new, $param)
    {
        $datetime = DateTime::createFromFormat($orig, $param);

        return $datetime->format($new);
    }
}
