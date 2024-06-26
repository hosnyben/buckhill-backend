<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Import Requests
use App\Http\Requests\UserRequest\UserLogin;
use App\Http\Requests\UserRequest\UserCreate;
use App\Http\Requests\UserRequest\UserEdit;
use App\Http\Requests\UserRequest\UserPasswordReset;

// Import Resource
use App\Http\Resources\UserResource;
use App\Http\Resources\OrderResource;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Notifications\PasswordResetCreated;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Get all users
            $users = User::paginate(config('app.pagination'));

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function create(UserCreate $request)
    {
        try {
            // Validate the request
            $validated = $request->validated();

            // Create the user
            $user = User::create($validated);

            // If route name is admin route, then mark it as admin
            if ($request->route()->getName() === 'userAdmin.create') {
                $user->is_admin = true;
                $user->save();
            }

            // Create a token
            $user->createToken('registerToken');

            return response()->apiSuccess(new UserResource($user));
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserEdit $request, $uuid = null)
    {
        try {
            // Validate the request
            $validated = $request->validated();

            // Update the user
            if($uuid) {
                $user = User::findOrFail($uuid);
            } else {
                $user = auth()->user();
            }

            $user->update($validated);

            return response()->apiSuccess(new UserResource($user));
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid = null)
    {
        try {
            // Delete the user
            if($uuid) {
                $user = User::findOrFail($uuid);
                $user->delete();
            } else {
                auth()->user()->delete();
            }

            return response()->apiSuccess([]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Authenticate the user.
     */
    public function login(UserLogin $request)
    {
        try {
            // Validate the request
            $validated = $request->validated();

            // Attempt to authenticate the user
            auth()->attempt($validated);

            // Generate a token
            auth()->user()->createToken('authToken');

            return response()->apiSuccess(['token' => auth()->user()->token]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        try {
            // Get the bearer token
            $token = $request->bearerToken();

            // Destroy the token
            auth()->user()->destroyToken($token);

            return response()->apiSuccess([]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Show the user.
     */
    public function show()
    {
        try {
            return response()->apiSuccess(new UserResource(auth()->user()));
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * List the orders of the user.
     */
    public function listOrders()
    {
        try {
            // Get the orders of the user in resources format
            return response()->apiSuccess(OrderResource::collection(auth()->user()->orders));
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'email' => 'required|email'
            ]);

            $user = User::where('email', $request->input('email'))->first();

            if($user) {
                // Remove existing tokens if any
                DB::table('password_resets')->where('email', $user->email)->delete();

                // Create a new token
                $token = Str::random(60);
                DB::table('password_resets')->insert([
                    'email' => $user->email,
                    'token' => $token,
                    'created_at' => now()
                ]);

                // Queue the notification
                $user->notify(new PasswordResetCreated($token));
            }

            // For security reasons, we will always return success
            return response()->apiSuccess([]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Reset password
     */

    public function resetPassword(UserPasswordReset $request, $token)
    {
        try {
            // Validate the request
            $validated = $request->validated();

            // Check if token from password_resets table exists and retrieve it, the token created_at should be within 1 hour
            $resetToken = DB::table('password_resets')->where('token', $token)->where('created_at', '>=', now()->subHour())->first();

            if($resetToken) {
                // Find the user
                $user = User::where('email', $resetToken->email)->first();

                // Update the password
                // No need to hash the password as it will be hashed automatically ($casts in User model)
                $user->update(['password' => $validated['password']]);

                // Remove the token
                DB::table('password_resets')->where('token', $token)->delete();
            } else {
                return response()->apiError('Invalid token', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // For security reasons, we will always return success
            return response()->apiSuccess([]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
