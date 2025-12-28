<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Services\SlugGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Links",
 *     description="API Endpoints for managing shortened links"
 * )
 */
class LinkController extends Controller
{
    public function __construct(
        private SlugGeneratorService $slugGenerator
    ) {}

    /**
     * @OA\Get(
     *     path="/api/links",
     *     summary="List all links for authenticated user",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by title or URL",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by field",
     *         @OA\Schema(type="string", enum={"title", "slug", "access_count", "created_at"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of links",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Link"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $request->user()->links();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('original_url', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['title', 'slug', 'access_count', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $perPage = min($request->input('per_page', 15), 100);

        return LinkResource::collection($query->paginate($perPage));
    }

    /**
     * @OA\Post(
     *     path="/api/links",
     *     summary="Create a new shortened link",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"original_url"},
     *             @OA\Property(property="original_url", type="string", example="https://example.com/very/long/url"),
     *             @OA\Property(property="slug", type="string", example="mylink", description="Optional custom slug (6-8 chars)"),
     *             @OA\Property(property="title", type="string", example="My Link", description="Optional title")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Link created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Link")
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(StoreLinkRequest $request): JsonResponse
    {
        $slug = $request->input('slug') ?: $this->slugGenerator->generate();

        $link = $request->user()->links()->create([
            'original_url' => $request->input('original_url'),
            'slug' => $slug,
            'title' => $request->input('title'),
        ]);

        return response()->json(new LinkResource($link), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/links/{id}",
     *     summary="Get a specific link",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Link details",
     *         @OA\JsonContent(ref="#/components/schemas/Link")
     *     ),
     *     @OA\Response(response=404, description="Link not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show(Request $request, int $link): JsonResponse
    {
        $linkModel = $request->user()->links()->findOrFail($link);

        return response()->json(new LinkResource($linkModel));
    }

    /**
     * @OA\Put(
     *     path="/api/links/{id}",
     *     summary="Update a link",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="original_url", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="title", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Link updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Link")
     *     ),
     *     @OA\Response(response=404, description="Link not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(StoreLinkRequest $request, int $link): JsonResponse
    {
        $linkModel = $request->user()->links()->findOrFail($link);

        $linkModel->update($request->only(['original_url', 'slug', 'title']));

        return response()->json(new LinkResource($linkModel->fresh()));
    }

    /**
     * @OA\Delete(
     *     path="/api/links/{id}",
     *     summary="Soft delete a link (move to trash)",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Link moved to trash",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Link moved to trash")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Link not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy(Request $request, int $link): JsonResponse
    {
        $linkModel = $request->user()->links()->findOrFail($link);
        $linkModel->delete();

        return response()->json(['message' => 'Link moved to trash']);
    }

    /**
     * @OA\Get(
     *     path="/api/links/trash",
     *     summary="List trashed links",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of trashed links",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Link"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function trash(Request $request): AnonymousResourceCollection
    {
        $links = $request->user()->links()->onlyTrashed()->paginate(15);

        return LinkResource::collection($links);
    }

    /**
     * @OA\Post(
     *     path="/api/links/{id}/restore",
     *     summary="Restore a link from trash",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Link restored successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Link")
     *     ),
     *     @OA\Response(response=404, description="Link not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function restore(Request $request, int $link): JsonResponse
    {
        $linkModel = $request->user()->links()->onlyTrashed()->findOrFail($link);
        $linkModel->restore();

        return response()->json(new LinkResource($linkModel));
    }

    /**
     * @OA\Delete(
     *     path="/api/links/{id}/force",
     *     summary="Permanently delete a link",
     *     tags={"Links"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Link permanently deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Link permanently deleted")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Link not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function forceDelete(Request $request, int $link): JsonResponse
    {
        $linkModel = $request->user()->links()->withTrashed()->findOrFail($link);
        $linkModel->forceDelete();

        return response()->json(['message' => 'Link permanently deleted']);
    }
}
