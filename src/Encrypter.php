<?php
/**
 * src/Encryption.php.
 *
 */

namespace ESolution\DBEncryption;

use ESolution\DBEncryption\Traits\Salty;

class Encrypter
{
    use Salty;

    /**
     * @param string $value
     *
     * @return string
     */
    public static function encrypt($value)
    {
        return openssl_encrypt($value, config('laravelDatabaseEncryption.encrypt_method'), self::getKey(), 0, $iv = '');
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function decrypt($value)
    {
        return openssl_decrypt($value, config('laravelDatabaseEncryption.encrypt_method'), self::getKey(), 0, $iv = '');
    }

    /**
     * @return string
     */
    protected static function getKey(): string
    {
        return (new self())->salt();
    }
}
