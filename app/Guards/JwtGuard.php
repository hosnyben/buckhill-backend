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
    /**
     * @var User | null $user The user.
     * @var UserProvider $provider The user provider.
     * @var Request $request The request.
     * @var Configuration $config The JWT configuration.
     */
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

            if(!method_exists($token, 'claims')) {
                return null;
            }

            $constraints = $this->config->validationConstraints();

            if(!empty($constraints)) {
                $this->config->validator()->assert($token, ...$constraints);
            }

            $uuid = $token->claims()->get('user_uuid');

            if(!$uuid) {
                return null;
            }

            $user = User::where('uuid', $uuid)->first();

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

    /**
     * Validate a user's credentials.
     *
     * @param array<mixed> $credentials User credentials.
     * @return bool Always returns false, as this method is not applicable for JWT Guard.
     */
    public function validate(array $credentials = []): bool
    {
        // This method is not applicable for JWT Guard
        return false;
    }

    public function hasUser()
    {
        return !is_null($this->user);
    }

    /**
     * Set the user.
     * @param \App\Models\User $user The user to set.
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Destroy the token.
     * @param \Lcobucci\JWT\Token $token The token to destroy.
     * @return void
     * @throws \Exception If the token is invalid.
     */
    private function validateToken($token)
    {
        if (!$this->user) {
            throw new \Exception('No user set');
        }

        if(!method_exists($token, 'claims')) {
            throw new \Exception('Invalid token');
        }

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

    /**
     * Attempt to authenticate a user using the given credentials.
     * @param array<mixed> $credentials User credentials.
     * @param bool $remember Whether to remember the user.
     * @return void
     * @throws \Exception If the authentication attempt fails.
     */
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
