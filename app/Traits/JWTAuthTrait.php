<?php

namespace App\Traits;

use App\Helpers\JwtHelper;

use Illuminate\Support\Facades\DB;

trait JWTAuthTrait
{
    public function createToken(string $expiration = '+1 week'): string
    {
        // Ensure $this->uuid is a non-empty string
        if (empty($this->uuid)) {
            return '';
        }

        $config = JwtHelper::getJwtConfiguration();
        $date = new \DateTimeImmutable();
        $uniqueID = uniqid();

        $token = $config->builder()
            ->issuedBy(config('jwt.issuer')) // Configures the issuer (iss claim)
            ->permittedFor(config('jwt.audience')) // Configures the audience (aud claim)
            ->identifiedBy($uniqueID) // Configures the id (jti claim)
            ->relatedTo($this->uuid) // Configures the subject (sub claim)
            ->issuedAt($date) // Configures the time that the token was issued (iat claim)
            ->canOnlyBeUsedAfter($date) // Configures the time that the token can be used (nbf claim)
            ->expiresAt($date->modify($expiration)) // Configures the expiration time of the token (exp claim)
            ->withClaim('user_uuid', $this->uuid) // Configures a new claim, called "user_uuid"
            ->getToken($config->signer(), $config->signingKey()); // Retrieves the generated token

        DB::table('jwt_tokens')->insert([
            'user_id' => $this->id,
            'unique_id' => $uniqueID,
            'token_title' => "Register Token ".$this->email,
            'restrictions' => null,
            'permissions' => null,
            'expires_at' => $date->modify($expiration),
            'last_used_at' => null,
            'refreshed_at' => null
        ]);

        return $token->toString();
    }

    public function destroyToken(string $tokenString): bool
    {
        try {
            $config = JwtHelper::getJwtConfiguration();

            if (empty($tokenString)) {
                return false;
            }

            $token = $config->parser()->parse($tokenString);

            if (!method_exists($token, 'claims')) {
                return false;
            }

            $uniqueID = $token->claims()->get('jti');

            DB::table('jwt_tokens')->where('unique_id', $uniqueID)->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
