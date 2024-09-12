<?php
/**
 * src/Traits/EncryptedAttribute.php.
 *
 */

namespace ESolution\DBEncryption\Traits;

use ESolution\DBEncryption\Builders\EncryptionEloquentBuilder;
use ESolution\DBEncryption\Encrypter;

trait EncryptedAttribute
{

    public static $enableEncryption = true;

    function __construct()
    {
        self::$enableEncryption = config('laravelDatabaseEncryption.enable_encryption');
    }

    /**
     * @param $key
     * @return bool
     */
    public function isEncryptable($key)
    {
        if (self::$enableEncryption) {
            return in_array($key, $this->encryptable);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getEncryptableAttributes()
    {
        return $this->encryptable;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($this->isEncryptable($key) && !empty($value)) {
            try {
                $value = Encrypter::decrypt($value);
            } catch (\Exception $th) {
            }
        }

        return $value;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->isEncryptable($key) && !empty($value)) {
            try {
                $value = Encrypter::encrypt($value);
            } catch (\Exception $th) {
            }
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        if ($attributes) {
            foreach ($attributes as $key => $value) {
                if ($this->isEncryptable($key) && !empty($value)) {
                    $attributes[$key] = $value;
                    try {
                        $attributes[$key] = Encrypter::decrypt($value);
                    } catch (\Exception $th) {
                    }
                }
            }
        }

        return $attributes;
    }

    // Extend EncryptionEloquentBuilder
    public function newEloquentBuilder($query)
    {
        return new EncryptionEloquentBuilder($query);
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    public function decryptAttribute(string $value = null): ?string
    {
        return !empty($value) ? Encrypter::decrypt($value) : $value;
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    public function encryptAttribute(string $value = null): ?string
    {
        return !empty($value) ? Encrypter::encrypt($value) : $value;
    }
}
