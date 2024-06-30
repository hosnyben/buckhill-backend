<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Post;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/main/blog",
     *     operationId="blog.index",
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
            // Get all posts
            $posts = Post::paginate(config('app.pagination'));

            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/main/blog/{slug}",
     *     operationId="blog.show",
     *     tags={"Main"},
     *     summary="Show blogpost",
     *     description="Show blogpost endpoint",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Blogpost slug",
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
    public function show(Post $post)
    {
        try {
            return response()->apiSuccess($post);
        } catch (\Exception $e) {
            return response()->apiError($e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
