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

// Import Resource
use App\Http\Resources\UserResource;

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
    public function update(UserEdit $request, User $user)
    {
        try {
            // Validate the request
            $validated = $request->validated();

            // Update the user
            $user->update($validated);

            return response()->apiSuccess(new UserResource($user));
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Delete the user
            $user->delete();

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
}
