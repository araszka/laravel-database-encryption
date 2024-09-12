<?php

return [
    /*
     * Enable/disable encryption of model attributes.
     */
    'enable_encryption'             => env('DB_ENCRYPTION', true),

    /*
     * Hashing method used for salt generation.
     */
    'hash_method'                   => strtolower(env('DB_ENCRYPTION_HASH', 'sha256')),

    /*
     * The method used for encryption.
     */
    'encrypt_method'                => strtolower(env('DB_ENCRYPTION_METHOD', 'aes-256-cbc')),

    /*
     * The encryption key.
     */
    'encrypt_key'                   => env('APP_KEY'),
];
