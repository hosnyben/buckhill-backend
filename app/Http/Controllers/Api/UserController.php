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
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/admin/user-listing",
     *     operationId="userAdmin.userListing",
     *     tags={"Admin endpoint"},
     *     summary="List available users",
     *     description="List available users endpoint",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
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
     * @param UserCreate $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/v1/admin/create",
     *     operationId="userAdmin.create",
     *     tags={"Admin endpoint"},
     *     summary="Create admin user",
     *     description="Create admin user endpoint and receive the confirmation with the token",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         request="Register",
     *         description="Register with user details",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"first_name", "last_name", "email", "password", "password_confirmation","address","phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="The user first name",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="The user last name",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="email",
     *                     description="The user email",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="The user password",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="The user password confirmation",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="avatar_uuid",
     *                     type="string",
     *                     description="The avatar UUID from the file table. The uuid  msut be from the file table",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="The address of the user",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="The phone number of the user",
     *                     default=""
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     *
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     operationId="user.create",
     *     tags={"User endpoint"},
     *     summary="Create regular user",
     *     description="Create regular user endpoint and receive the confirmation with the token",
     *     @OA\RequestBody(
     *         request="Register",
     *         description="Register with user details",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"first_name", "last_name", "email", "password", "password_confirmation","address","phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="The user first name",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="The user last name",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="email",
     *                     description="The user email",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="The user password",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="The user password confirmation",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="avatar_uuid",
     *                     type="string",
     *                     description="The avatar UUID from the file table. The uuid  msut be from the file table",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="The address of the user",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="The phone number of the user",
     *                     default=""
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function create(UserCreate $request)
    {
        try {
            // Validate the request
            $validated = $request->validated();

            // Create the user
            $user = User::create($validated);

            // If route name is admin route, then mark it as admin
            if ($request->route()?->getName() === 'userAdmin.create') {
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
     * @param UserEdit $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Put(
     *     path="/api/v1/admin/user-edit/{uuid}",
     *     operationId="userAdmin.userEdit",
     *     tags={"Admin endpoint"},
     *     summary="Edit a user",
     *     description="Edit a user endpoint and receive the confirmation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="User uuid",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         request="Edit",
     *         description="Update user details",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="The user first name",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="The user last name",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="email",
     *                     description="The user email",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="The user password",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="The user password confirmation",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="avatar_uuid",
     *                     type="string",
     *                     description="The avatar UUID from the file table. The uuid  msut be from the file table",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="The address of the user",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="The phone number of the user",
     *                     default=""
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     * 
     * @OA\Put(
     *     path="/api/v1/user/edit/",
     *     operationId="user.update",
     *     tags={"User endpoint"},
     *     summary="Edit authenticated user",
     *     description="Edit authenticated user endpoint and receive the confirmation",
     *     security={{"bearerAuth":{}}},
     * 
     *     @OA\RequestBody(
     *         request="Edit",
     *         description="Update user details",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="The user first name",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="The user last name",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="email",
     *                     description="The user email",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="The user password",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="The user password confirmation",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="avatar_uuid",
     *                     type="string",
     *                     description="The avatar UUID from the file table. The uuid  msut be from the file table",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="The address of the user",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="The phone number of the user",
     *                     default=""
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
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

            $user?->update($validated);

            return response()->apiSuccess(new UserResource($user));
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Delete(
     *     path="/api/v1/admin/user-delete/{uuid}",
     *     operationId="userAdmin.userDelete",
     *     tags={"Admin endpoint"},
     *     summary="Delete a user",
     *     description="Delete a user endpoint and receive the confirmation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="User uuid",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     * 
     * @OA\Delete(
     *     path="/api/v1/user/",
     *     operationId="user.destroy",
     *     tags={"User endpoint"},
     *     summary="Delete authenticated user",
     *     description="Delete authenticated user endpoint and receive the confirmation",
     *     security={{"bearerAuth":{}}},
     * 
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function destroy($uuid = null)
    {
        try {
            // Delete the user
            if($uuid) {
                $user = User::findOrFail($uuid);
                $user->delete();
            } else {
                auth()->user()?->delete();
            }

            return response()->apiSuccess([]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Authenticate the user.
     * @param UserLogin $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/v1/admin/login",
     *     operationId="userAdmin.login",
     *     tags={"Admin endpoint"},
     *     summary="Login as admin user",
     *     description="Login as admin user endpoint and receive the token",
     * 
     *     @OA\RequestBody(
     *         request="Login",
     *         description="Login using Email/Password",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="email",
     *                     description="The user email",
     *                     default="admin@buckhill.co.uk"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="The user password",
     *                     default="admin"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     * 
     * @OA\Post(
     *     path="/api/v1/user/login",
     *     operationId="user.login",
     *     tags={"User endpoint"},
     *     summary="Login as user",
     *     description="Login as user endpoint and receive the token",
     * 
     *     @OA\RequestBody(
     *         request="CreateBrand",
     *         description="Create brand request body",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="email",
     *                     description="The user email",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="The user password",
     *                     default="userpassword"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function login(UserLogin $request)
    {
        try {
            // Validate the request
            $validated = $request->validated();

            // Attempt to authenticate the user
            auth()->attempt($validated);

            // Generate a token
            // Check if admin endpoint or user endpoint
            if (
                ($request->route()?->getName() === 'userAdmin.login' && auth()->user()?->is_admin) || 
                ($request->route()?->getName() === 'user.login' && ! auth()->user()?->is_admin)   
            ) {
                auth()->user()?->createToken('authToken');
            } else {
                return response()->apiError(new \Exception('Unauthorized'), Response::HTTP_UNAUTHORIZED);
            }

            return response()->apiSuccess(['token' => auth()->user()?->token]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Logout the user.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/admin/logout",
     *     operationId="userAdmin.logout",
     *     tags={"Admin endpoint"},
     *     summary="Logout user",
     *     description="Logout user endpoint and receive the confirmation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     *
     * @OA\Get(
     *     path="/api/v1/user/logout",
     *     operationId="user.logout",
     *     tags={"User endpoint"},
     *     summary="Logout user",
     *     description="Logout user endpoint and receive the confirmation",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            // Get the bearer token
            $token = $request->bearerToken();

            // Destroy the token
            if($token) {
                auth()->user()?->destroyToken($token);
            }

            return response()->apiSuccess([]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Show the user.
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/user/",
     *     operationId="user.show",
     *     tags={"User endpoint"},
     *     summary="Show user details",
     *     description="Show user details endpoint",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
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
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/user/orders",
     *     operationId="user.orders",
     *     tags={"User endpoint"},
     *     summary="List user orders",
     *     description="List user orders endpoint",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function listOrders()
    {
        try {
            // Get the orders of the user in resources format
            return response()->apiSuccess(OrderResource::collection(auth()->user()?->orders));
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Forgot password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/v1/user/forgot-password",
     *     operationId="user.forgotPassword",
     *     tags={"User endpoint"},
     *     summary="Request reset password",
     *     description="Request reset password endpoint and receive the token",
     * 
     *     @OA\RequestBody(
     *         request="Forget Password",
     *         description="Request password using email",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="email",
     *                     description="The user email",
     *                     default="admin@buckhill.co.uk"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
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
     * @param string $token
     * @param UserPasswordReset $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/v1/user/reset-password-token/{token}",
     *     operationId="user.resetPassword",
     *     tags={"User endpoint"},
     *     summary="Reset password with token path",
     *     description="Reset password endpoint and receive the confirmation",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         description="User token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         request="Reset password",
     *         description="Reset password using token path",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"password", "password_confirmation"},
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="The user password",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="The user password confirmation",
     *                     default=""
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function resetPassword(UserPasswordReset $request, $token)
    {
        try {
            // Validate the request
            $validated = $request->validated();

            // Check if token from password_resets table exists and retrieve it, the token created_at should be within 1 hour
            $resetToken = DB::table('password_resets')->where('token', $token)->where('created_at', '>=', now()->subHour())->first();

            if($resetToken && property_exists($resetToken, 'email')) {
                // Find the user
                $user = User::where('email', $resetToken->email)->first();

                if(! $user) {
                    return response()->apiError(new \Exception('Invalid token'), Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                // Update the password
                // No need to hash the password as it will be hashed automatically ($casts in User model)
                $user->update(['password' => $validated['password']]);

                // Remove the token
                DB::table('password_resets')->where('token', $token)->delete();
            } else {
                return response()->apiError(new \Exception('Invalid token'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // For security reasons, we will always return success
            return response()->apiSuccess([]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
