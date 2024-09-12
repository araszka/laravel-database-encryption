<?php
/**
 * src/Providers/EncryptServiceProvider.php.
 *
 */

namespace ESolution\DBEncryption\Providers;

use ESolution\DBEncryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class DBEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * This method is called after all other service providers have
     * been registered, meaning you have access to all other services
     * that have been registered by the framework.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('unique_encrypted', function ($attribute, $value, $parameters, $validator) {
            // Initialize
            $withFilter = count($parameters) > 3;
            $ignore_id  = $parameters[2] ?? '';

            // Check using normal checker
            $data = DB::table($parameters[0])
                ->beforeQuery(fn() => Encrypter::setBlockEncryptionModeStatement())
                ->whereRaw(Encrypter::getDecryptSql($parameters[1]) . " = ?", [$value]);

            $data = $ignore_id != '' ? $data->where('id', '!=', $ignore_id) : $data;

            if ($withFilter) {
                $data->where($parameters[3], $parameters[4]);
            }

            if ($data->first()) {
                return false;
            }

            return true;
        });

        Validator::extend('exists_encrypted', function ($attribute, $value, $parameters, $validator) {
            // Initialize
            $withFilter = count($parameters) > 3;
            if (!$withFilter) {
                $ignore_id = $parameters[2] ?? '';
            } else {
                $ignore_id = $parameters[4] ?? '';
            }

            // Check using normal checker
            $data = DB::table($parameters[0])
                ->beforeQuery(fn() => Encrypter::setBlockEncryptionModeStatement())
                ->whereRaw(Encrypter::getDecryptSql($parameters[1]) . " = ?", [$value]);

            $data = $ignore_id != '' ? $data->where('id', '!=', $ignore_id) : $data;

            if ($withFilter) {
                $data->where($parameters[2], $parameters[3]);
            }

            if ($data->first()) {
                return true;
            }

            return false;
        });
    }
}
