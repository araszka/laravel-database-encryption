<?php
/**
 * src/Builders/EncryptionEloquentBuilder.php.
 *
 */

namespace ESolution\DBEncryption\Builders;

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
        $operation = isset($value) ? $opOrValue : '=';
        $value     = $value ?: $opOrValue;

        return self::whereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$column}`), '{$this->salt()}') USING utf8mb4) {$operation} ? ", [$value]);
    }

    /**
     * @param string      $column
     * @param string      $opOrValue
     * @param string|null $value
     * @return self
     */
    public function orWhereEncrypted(string $column, string $opOrValue, string $value = null): self
    {
        $operation = isset($value) ? $opOrValue : '=';
        $value     = $value ?: $opOrValue;

        return self::orWhereRaw("CONVERT(AES_DECRYPT(FROM_BASE64(`{$column}`), '{$this->salt()}') USING utf8mb4) {$operation} ? ", [$value]);
    }

    /**
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function orderByEncrypted(string $column, string $direction = 'asc'): self
    {
        return self::orderByRaw("CONVERT(AES_DECRYPT(FROM_bASE64(`{$column}`), '{$this->salt()}') USING utf8mb4) {$direction}");
    }
}
