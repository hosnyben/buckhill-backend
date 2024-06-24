<?php

namespace App\Guards;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;


use App\Helpers\JwtHelper;

use Illuminate\Support\Facades\DB;

class JwtGuard implements Guard
{
    protected $user;
    protected $provider;
    protected $request;
    protected $config;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;

        $this->config = JwtHelper::getJwtConfiguration();
    }

    public function check()
    {
        return !is_null($this->user());
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $token = $this->request->bearerToken();
        $token = $this->config->parser()->parse($token);

        if (!$token) {
            return null;
        }

        try {
            $constraints = $this->config->validationConstraints();

            if(!empty($constraints)) {
                $this->config->validator()->assert($token, ...$constraints);
            }

            $this->user = $this->provider->retrieveByCredentials(['uuid' => $token->claims()->get('user_uuid')]);

            $this->validateToken($token);

            return $this->user;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function id()
    {
        return $this->user() ? $this->user()->getAuthIdentifier() : null;
    }

    public function validate(array $credentials = [])
    {
        // This method is not applicable for JWT Guard
        return false;
    }

    public function hasUser()
    {
        return !is_null($this->user);
    }

    public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        $this->user = $user;
    }

    private function validateToken($token)
    {
        $recordExists = DB::table('jwt_tokens')
                            ->where([
                                'unique_id' => $token->claims()->get('jti'),
                                'user_id' => $this->user->id
                            ])
                            ->where('expires_at', '>', now())
                            ->exists();

        if(!$recordExists) {
            throw new \Exception('Invalid token');
        }
    }
}
