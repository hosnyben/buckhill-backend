<?php

namespace App\Helpers;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

use Illuminate\Support\Facades\Storage;

class JwtHelper
{
    public static function getJwtConfiguration()
    {
        return Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(Storage::path('jwt/private.pem')),
            InMemory::file(Storage::path('jwt/public.pem'))
        );
    }
}
