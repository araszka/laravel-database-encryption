<?php

namespace ESolution\DBEncryption\Traits;

trait Salty
{
    /**
     * @return string
     */
    private function salt(): string
    {
        return substr(hash(config('laravelDatabaseEncryption.hash_method'), config('laravelDatabaseEncryption.encrypt_key')), 0, 16);
    }
}
