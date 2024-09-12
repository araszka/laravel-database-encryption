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
        $decryptSql = Encrypter::getDecryptSql($column, $this->salt());

        return $this->beforeQuery(fn() => Encrypter::setBlockEncryptionModeStatement())
            ->whereRaw("$decryptSql $operation ?", [$value]);
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
        $decryptSql = Encrypter::getDecryptSql($column, $this->salt());

        return $this->beforeQuery(fn() => Encrypter::setBlockEncryptionModeStatement())
            ->orWhereRaw("$decryptSql $operation ?", [$value]);
    }

    /**
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function orderByEncrypted(string $column, string $direction = 'asc'): self
    {
        return $this->beforeQuery(fn() => Encrypter::setBlockEncryptionModeStatement())
            ->orderByRaw(Encrypter::getDecryptSql($column, $this->salt()) . " $direction");
    }
}
