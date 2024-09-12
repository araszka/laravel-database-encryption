<?php

return [
    /*
     * Enable/disable encryption of model attributes.
     */
    'enable_encryption'             => env('DB_ENCRYPTION', true),

    /*
     * Hashing method used for salt generation.
     */
    'hash_method'                   => env('DB_ENCRYPTION_HASH', 'sha256'),

    /*
     * The method used for encryption.
     */
    'encrypt_method'                => env('DB_ENCRYPTION_METHOD', 'aes-256-cbc'),

    /*
     * Initialization vector for AES (16 byte).
     * Required for: CBC, CFB1, CFB8, CFB128, and OFB.
     */
    'encrypt_initialization_vector' => env('DB_ENCRYPTION_INITIALIZATION_VECTOR', substr(strrev(sha1(env('APP_KEY'))), -16)),

    /*
     * The encryption key.
     */
    'encrypt_key'                   => env('APP_KEY'),
];
