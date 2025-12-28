<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Statistics",
 *     description="User statistics endpoints"
 * )
 */
class StatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/stats",
     *     summary="Get user statistics",
     *     tags={"Statistics"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_links", type="integer", example=71),
     *             @OA\Property(property="total_views", type="integer", example=249),
     *             @OA\Property(property="total_clicks", type="integer", example=53),
     *             @OA\Property(property="avg_ctr", type="number", format="float", example=21.3)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $links = $user->links();

        $totalLinks = $links->count();
        $totalViews = $links->sum('access_count');
        $totalClicks = $totalViews; // In this context, views = clicks

        // Calculate average CTR (clicks/views * 100)
        // For simplicity, we'll calculate based on total numbers
        $avgCtr = $totalViews > 0 ? round(($totalClicks / max($totalViews, 1)) * 100, 2) : 0;

        return response()->json([
            'total_links' => $totalLinks,
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'avg_ctr' => $avgCtr,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/stats/detailed",
     *     summary="Get detailed statistics",
     *     tags={"Statistics"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Detailed statistics with top links",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_links", type="integer"),
     *             @OA\Property(property="total_views", type="integer"),
     *             @OA\Property(property="total_clicks", type="integer"),
     *             @OA\Property(property="avg_ctr", type="number"),
     *             @OA\Property(property="links_today", type="integer"),
     *             @OA\Property(property="views_today", type="integer"),
     *             @OA\Property(
     *                 property="top_links",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Link")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function detailed(Request $request): JsonResponse
    {
        $user = $request->user();
        $links = $user->links();

        $totalLinks = $links->count();
        $totalViews = $links->sum('access_count');
        $totalClicks = $totalViews;
        $avgCtr = $totalViews > 0 ? round(($totalClicks / max($totalViews, 1)) * 100, 2) : 0;

        // Links created today
        $linksToday = $user->links()->whereDate('created_at', today())->count();

        // Views today (from access logs)
        $viewsToday = $user->links()
            ->withCount(['accessLogs' => function ($query) {
                $query->whereDate('accessed_at', today());
            }])
            ->get()
            ->sum('access_logs_count');

        // Top 5 links by access count
        $topLinks = $user->links()
            ->orderBy('access_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($link) {
                return [
                    'id' => $link->id,
                    'title' => $link->title,
                    'slug' => $link->slug,
                    'short_url' => $link->short_url,
                    'access_count' => $link->access_count,
                ];
            });

        return response()->json([
            'total_links' => $totalLinks,
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'avg_ctr' => $avgCtr,
            'links_today' => $linksToday,
            'views_today' => $viewsToday,
            'top_links' => $topLinks,
        ]);
    }
}
