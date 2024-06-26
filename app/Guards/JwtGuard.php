<?php

namespace App\Guards;

use Lcobucci\JWT\Configuration;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

use App\Helpers\JwtHelper;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class JwtGuard implements Guard
{
    protected $user;
    protected UserProvider $provider;
    protected Request $request;
    protected Configuration $config;

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
        if ($this->hasUser()) {
            return $this->user;
        }

        try {

            $token = $this->request->bearerToken();

            if (!$token) {
                return null;
            }

            $token = $this->config->parser()->parse($token);

            $constraints = $this->config->validationConstraints();

            if(!empty($constraints)) {
                $this->config->validator()->assert($token, ...$constraints);
            }

            $user = $this->provider->retrieveByCredentials(['uuid' => $token->claims()->get('user_uuid')]);

            if(!$user) {
                return null;
            }

            $this->setUser($user);
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

    public function validate(array $credentials = []): bool
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
                                'user_uuid' => $this->user->uuid
                            ])
                            ->where('expires_at', '>', now())
                            ->exists();

        if(!$recordExists) {
            throw new \Exception('Invalid token');
        }
    }

    public function attempt(array $credentials = [], $remember = false)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception(__('auth.failed'));
        } else {
            $this->setUser($user);
        }
    }
}
