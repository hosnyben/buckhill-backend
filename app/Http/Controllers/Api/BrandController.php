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
