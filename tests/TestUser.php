<?php

namespace ESolution\DBEncryption\Tests;

use ESolution\DBEncryption\Tests\Database\Factories\TestUserFactory;
use ESolution\DBEncryption\Traits\ExtendedEncryption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static TestUserFactory factory()
 */
class TestUser extends Model
{
    use HasFactory, ExtendedEncryption;

    /**
     * @var string[]
     */
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'email' => 'encrypted',
        'name'  => 'encrypted',
    ];

    /**
     * @return TestUserFactory
     */
    protected static function newFactory(): TestUserFactory
    {
        return TestUserFactory::new();
    }
}
