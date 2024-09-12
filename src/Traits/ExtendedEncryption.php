<?php
/**
 * src/Traits/EncryptedAttribute.php.
 *
 */

namespace ESolution\DBEncryption\Traits;

use ESolution\DBEncryption\Builders\EncryptionEloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @method static EncryptionEloquentBuilder query()
 * @method static EncryptionEloquentBuilder whereEncrypted(string $column, string $opOrValue, string $value = null)
 * @method static EncryptionEloquentBuilder orWhereEncrypted(string $column, string $opOrValue, string $value = null)
 * @method static EncryptionEloquentBuilder whereInEncrypted(string $column, array $values)
 * @method static EncryptionEloquentBuilder orderByEncrypted(string $column, string $direction = 'asc')
 */
trait ExtendedEncryption
{
    /**
     * @param QueryBuilder $query
     * @return EncryptionEloquentBuilder
     */
    public function newEloquentBuilder(QueryBuilder $query): EncryptionEloquentBuilder
    {
        return new EncryptionEloquentBuilder($query);
    }
}
