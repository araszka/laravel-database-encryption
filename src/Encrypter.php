<?php
/**
 * src/Encryption.php.
 *
 */

namespace ESolution\DBEncryption;

use ESolution\DBEncryption\Traits\Salty;
use Illuminate\Support\Facades\DB;

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
        return openssl_encrypt($value, config('laravelDatabaseEncryption.encrypt_method'), self::getKey(), 0, config('laravelDatabaseEncryption.encrypt_initialization_vector'));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function decrypt($value)
    {
        return openssl_decrypt($value, config('laravelDatabaseEncryption.encrypt_method'), self::getKey(), 0, config('laravelDatabaseEncryption.encrypt_initialization_vector'));
    }

    /**
     * @return string
     */
    public static function setBlockEncryptionModeStatement(): string
    {
        return DB::statement("SET block_encryption_mode = ?;", [config('laravelDatabaseEncryption.encrypt_method')]);
    }

    /**
     * @return string
     */
    protected static function getKey(): string
    {
        return (new self())->salt();
    }
}
