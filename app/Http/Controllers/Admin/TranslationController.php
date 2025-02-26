<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/**
 * @OA\Get(
 *     path="/api/translations/preview",
 *     summary="Preview translation related content",
 *     tags={"Translations"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID of the translation to preview"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful preview of the translation content",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(type="string", example="<html>Rendered preview content</html>"),
 *                 @OA\Schema(type="string", example="Brak dania")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized access",
 *         @OA\JsonContent(type="string", example="Brak dostępu")
 *     )
 * )
 */
class TranslationController extends Controller
{
    public function preview(Request $request)
    {
        if (auth()->check()) {
            $preview_row = null;
            $view = null;
            $translation_row = DB::connection('tenant')->table('ltm_translations')->where('id', $request->get('id'))->first();
            if ($translation_row) {
                switch($translation_row->object_table) {
                    case 'dishes':
                        $preview_row = \App\Models\Dish::where('id', $translation_row->object_id)->first();
                        $view = $preview_row ? view('admin.dishes.preview', ['data'=>$preview_row])->render() : 'Brak dania';
                }
            }

            return JsonResponse::create($view);
        } else {
            return JsonResponse::create('Brak dostępu');
        }
    }
}
