<?php

namespace ESolution\DBEncryption\Traits;

trait Salty
{
    /**
     * @return string
     */
    private function salt(): string
    {
        return substr(hash('sha256', config('app.key')), 0, 16);
    }
}
