<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests\BrandRequest\BrandCreate;
use App\Http\Requests\BrandRequest\BrandEdit;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/brands",
     *     operationId="brand.index",
     *     tags={"Brands"},
     *     summary="List brands",
     *     description="List brands endpoint",
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
    public function index()
    {
        try {
            $brands = Brand::paginate(config('app.pagination'));
            return response()->json($brands);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     * @param Brand $brand
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/brand/{slug}",
     *     operationId="brand.show",
     *     tags={"Brands"},
     *     summary="Show brand",
     *     description="Show brand endpoint",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Brand slug",
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
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function show(Brand $brand)
    {
        try {
            return response()->apiSuccess($brand);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @param BrandCreate $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/v1/brand/create",
     *     operationId="brand.create",
     *     tags={"Brands"},
     *     summary="Create brand",
     *     description="Create brand endpoint",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         request="CreateBrand",
     *         description="Create brand request body",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="The title of the brand",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="slug",
     *                     type="string",
     *                     description="The slug of the brand, auto-generated if not provided",
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
    public function create(BrandCreate $request)
    {
        try {
            $validated = $request->validated();
            $brand = Brand::create($validated);

            return response()->apiSuccess($brand);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param BrandEdit $request
     * @param Brand $brand
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/api/v1/brand/{slug}",
     *     operationId="brand.update",
     *     tags={"Brands"},
     *     summary="Update brand",
     *     description="Create brand endpoint",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Brand slug",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         request="CreateBrand",
     *         description="Create brand request body",
     *         required=true,
     * 
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="The title of the brand",
     *                     default=""
     *                 ),
     *                 @OA\Property(
     *                     property="slug",
     *                     type="string",
     *                     description="The slug of the brand, auto-generated if not provided",
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
    public function update(BrandEdit $request, Brand $brand)
    {
        try {
            // Validate the request
            $validated = $request->validated();
            $brand->update($validated);

            return response()->apiSuccess($brand);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param Brand $brand
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/api/v1/brand/{slug}",
     *     operationId="brand.destroy",
     *     tags={"Brands"},
     *     summary="Update brand",
     *     description="Create brand endpoint",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Brand slug",
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
     *         response="422",
     *         description="Error processing the request",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function destroy(Brand $brand)
    {
        try {
            $brand->delete();

            return response()->apiSuccess([]);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}