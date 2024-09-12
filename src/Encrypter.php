<?php
/**
 * src/Encryption.php.
 *
 */

namespace ESolution\DBEncryption;

use ESolution\DBEncryption\Traits\Salty;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Facades\DB;

class Encrypter
{
    use Salty;

    /**
     * The supported cipher algorithms and their properties.
     *
     * @var array
     */
    private static $supportedCiphers = [
        'aes-128-cbc' => ['size' => 16, 'aead' => false],
        'aes-256-cbc' => ['size' => 32, 'aead' => false],
        'aes-128-gcm' => ['size' => 16, 'aead' => true],
        'aes-256-gcm' => ['size' => 32, 'aead' => true],
    ];

    /**
     * @param string $value
     * @return string|false
     * @throws \Exception
     */
    public static function encrypt(string $value): string|false
    {
        $cipher = strtolower(config('app.cipher'));
        $iv     = random_bytes(openssl_cipher_iv_length($cipher));
        $tag    = null;
        $key    = self::getKey();

        $value = openssl_encrypt(
            data: $value,
            cipher_algo: $cipher,
            passphrase: $key,
            iv: $iv,
            tag: $tag
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        $iv  = base64_encode($iv);
        $tag = base64_encode($tag ?: '');

        $mac = self::$supportedCiphers[strtolower($cipher)]['aead']
            ? '' // For AEAD-algorithms, the tag / MAC is returned by openssl_encrypt...
            : self::hash($iv, $value, $key);

        $json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * @param string $value
     * @return string|false
     */
    public static function decrypt(string $value): string|false
    {
        $cipher = strtolower(config('app.cipher'));
        $tag = null;
        $key = self::getKey();

        $payload = json_decode(base64_decode($value), true);
        $iv      = base64_decode($payload['iv']);

        $tag = empty($payload['tag']) ? null : base64_decode($payload['tag']);

//        $this->ensureTagIsValid(
//            $tag = empty($payload['tag']) ? null : base64_decode($payload['tag'])
//        );

        $foundValidMac = false;

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
//        foreach ($this->getAllKeys() as $key) {
//            if (
//                $this->shouldValidateMac() &&
//                ! ($foundValidMac = $foundValidMac || $this->validMacForKey($payload, $key))
//            ) {
//                continue;
//            }

        $decrypted = \openssl_decrypt(
            $payload['value'], strtolower($cipher), $key, 0, $iv, $tag ?? ''
        );

//            if ($decrypted !== false) {
//                break;
//            }
//        }

//        if ($this->shouldValidateMac() && ! $foundValidMac) {
//            throw new DecryptException('The MAC is invalid.');
//        }

        if (($decrypted ?? false) === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $decrypted;
    }

    /**
     * @return string
     */
    public static function setBlockEncryptionModeStatement(): string
    {
        return DB::statement("SET block_encryption_mode = ?;", [strtolower(config('app.cipher'))]);
    }

    /**
     * @return string
     */
    protected static function getKey(): string
    {
        return (new self())->salt();
    }

    /**
     * Create a MAC for the given value.
     *
     * @param string $iv
     * @param string $value
     * @param string $key
     * @return string
     */
    protected static function hash(string $iv, string $value, string $key): string
    {
        return hash_hmac('sha256', $iv . $value, $key);
    }

    /**
     * @param string $column
     * @param string $salt
     * @return string
     */
    public static function getDecryptSql(string $column, string $salt): string
    {
        $jsonPart  = "CONVERT(FROM_BASE64(`$column`) USING utf8mb4)";
        $ivPart    = "FROM_BASE64(JSON_UNQUOTE(JSON_EXTRACT($jsonPart, '$.iv')))";
        $valuePart = "FROM_BASE64(JSON_UNQUOTE(json_extract($jsonPart, '$.value')))";

        return "AES_DECRYPT($valuePart, '$salt', $ivPart)";
    }
}
