<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Response;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/main/promotions",
     *     operationId="promotions.index",
     *     tags={"Main"},
     *     summary="List blogposts",
     *     description="List blogposts endpoint",
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
            // Get all promotions
            $promotions = Promotion::paginate(config('app.pagination'));

            return response()->json($promotions);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
