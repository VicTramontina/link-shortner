<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Redirect",
 *     description="URL redirection endpoint"
 * )
 */
class RedirectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/{slug}",
     *     summary="Redirect to original URL",
     *     tags={"Redirect"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="The shortened link slug",
     *         @OA\Schema(type="string", example="abc123")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to original URL"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Link not found"
     *     )
     * )
     */
    public function redirect(Request $request, string $slug): RedirectResponse
    {
        $link = Link::where('slug', $slug)->firstOrFail();

        // Log the access
        AccessLog::create([
            'link_id' => $link->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accessed_at' => now(),
        ]);

        // Increment access count
        $link->incrementAccessCount();

        return redirect()->away($link->original_url);
    }
}
