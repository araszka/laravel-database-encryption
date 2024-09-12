<?php
/**
 * src/Builders/EncryptionEloquentBuilder.php.
 *
 */

namespace ESolution\DBEncryption\Builders;

use ESolution\DBEncryption\Encrypter;
use ESolution\DBEncryption\Traits\Salty;
use Illuminate\Database\Eloquent\Builder;

class EncryptionEloquentBuilder extends Builder
{
    use Salty;

    /**
     * @param string      $column
     * @param string      $opOrValue
     * @param string|null $value
     * @return self
     */
    public function whereEncrypted(string $column, string $opOrValue, string $value = null): self
    {
        $operation  = isset($value) ? $opOrValue : '=';
        $value      = $value ?: $opOrValue;
        $initVector = config('laravelDatabaseEncryption.encrypt_initialization_vector');

        return $this->beforeQuery(fn() => Encrypter::blockEncryptionModeStatement())
            ->whereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$column}`), '{$this->salt()}', '{$initVector}') USING utf8mb4) {$operation} ? ", [$value]);
    }

    /**
     * @param string      $column
     * @param string      $opOrValue
     * @param string|null $value
     * @return self
     */
    public function orWhereEncrypted(string $column, string $opOrValue, string $value = null): self
    {
        $operation  = isset($value) ? $opOrValue : '=';
        $value      = $value ?: $opOrValue;
        $initVector = config('laravelDatabaseEncryption.encrypt_initialization_vector');

        return $this->beforeQuery(fn() => Encrypter::blockEncryptionModeStatement())
            ->orWhereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$column}`), '{$this->salt()}', '{$initVector}') USING utf8mb4) {$operation} ? ", [$value]);
    }

    /**
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function orderByEncrypted(string $column, string $direction = 'asc'): self
    {
        $initVector = config('laravelDatabaseEncryption.encrypt_initialization_vector');

        return $this->beforeQuery(fn() => Encrypter::blockEncryptionModeStatement())
            ->orderByRaw("CONVERT(AES_DECRYPT(FROM_bASE64(`{$column}`), '{$this->salt()}', '{$initVector}') USING utf8mb4) {$direction}");
    }
}
