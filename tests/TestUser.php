<?php

namespace ESolution\DBEncryption\Tests;

use ESolution\DBEncryption\Tests\Database\Factories\TestUserFactory;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestUser extends Model
{
    use HasFactory, EncryptedAttribute;

    /**
     * @var string[]
     */
    protected $guarded = [
        'id',
    ];

    /**
     * @var string[]
     */
    protected $encryptable = [
        'email',
        'name'
    ];

    /**
     * @var string[]
     */
    protected $camelcase = [
        'name',
    ];

    /**
     * @return TestUserFactory
     */
    protected static function newFactory()
    {
        return TestUserFactory::new();
    }
}
