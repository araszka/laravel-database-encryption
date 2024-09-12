<?php

return [
    'enable_encryption' => true,
    'hash_method'       => env('DB_ENCRYPTION_HASH', 'sha256'),
    'encrypt_method'    => env('DB_ENCRYPTION_METHOD', 'aes-128-ecb'),
    'encrypt_key'       => env('APP_KEY'),
];