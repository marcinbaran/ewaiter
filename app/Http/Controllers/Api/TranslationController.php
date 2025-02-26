<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
/**
 * @OA\Tag(
 *     name="Translations",
 *     description="API Endpoints for Translations."
 * )
 */
class TranslationController extends ApiController
{
    public function __construct()
    {
    }
    /**
     * @OA\Get(
     *     path="/api/translations",
     *     operationId="getTranslations",
     *     tags={"Translations"},
     *     summary="Get collection of translations",
     *     description="Retrieve a collection of translations based on query parameters.",
     *     @OA\Parameter(
     *         name="group",
     *         in="query",
     *         description="Translation group (e.g., 'orders')",
     *         required=false,
     *         @OA\Schema(type="string", example="orders")
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         description="Locale code (e.g., 'pl')",
     *         required=false,
     *         @OA\Schema(type="string", example="pl")
     *     ),
     *     @OA\Parameter(
     *         name="simple",
     *         in="query",
     *         description="Return only key with value",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1059),
     *                 @OA\Property(property="status", type="integer", example=0),
     *                 @OA\Property(property="locale", type="string", example="cz"),
     *                 @OA\Property(property="group", type="string", example="additions"),
     *                 @OA\Property(property="key", type="string", example="Frytki belgijskie"),
     *                 @OA\Property(property="value", type="string", example="test czeski hranulky"),
     *                 @OA\Property(property="object_table", type="string", example="additions"),
     *                 @OA\Property(property="object_column", type="string", example="name"),
     *                 @OA\Property(property="object_id", type="integer", example=6),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2020-05-06 15:24:58"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2020-06-01 09:31:01")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $translations_db = DB::connection('tenant')->table('ltm_translations');
        if ($request->has('group')) {
            $translations_db->where('group', $request->get('group'));
        }
        if ($request->has('locale')) {
            $translations_db->where('locale', $request->get('locale'));
        }
        if ($request->get('simple')) {
            $translations = $translations_db->orderBy('group')->select(['key', 'value'])->get();
        } else {
            $translations = $translations_db->orderBy('group')->get();
        }

        return JsonResponse::create($translations);
    }
}
