<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="WorktimeRequest",
 *     type="object",
 *     @OA\Property(
 *         property="method",
 *         type="string",
 *         description="Metoda HTTP, np. GET, POST, PUT, DELETE.",
 *         example="GET"
 *     ),
 *     @OA\Property(
 *         property="rules",
 *         type="object",
 *         description="Reguły walidacji dla żądania.",
 *         @OA\Property(
 *             property="GET",
 *             type="object",
 *             description="Reguły walidacji dla metody GET.",
 *             @OA\Property(
 *                 property="date",
 *                 type="array",
 *                 @OA\Items(
 *                     type="string",
 *                     format="date",
 *                     description="Data w formacie YYYY-MM-DD.",
 *                     example="2024-07-31"
 *                 ),
 *                 description="Tablica dat do filtrowania.",
 *                 example={"2024-07-31", "2024-08-01"}
 *             ),
 *             @OA\Property(
 *                 property="date.*",
 *                 type="string",
 *                 format="date",
 *                 description="Pojedyncza data w formacie YYYY-MM-DD.",
 *                 example="2024-07-31"
 *             )
 *         ),
 *         @OA\Property(
 *             property="POST",
 *             type="object",
 *             description="Brak reguł walidacji dla metody POST.",
 *             example={}
 *         ),
 *         @OA\Property(
 *             property="PUT",
 *             type="object",
 *             description="Brak reguł walidacji dla metody PUT.",
 *             example={}
 *         ),
 *         @OA\Property(
 *             property="DELETE",
 *             type="object",
 *             description="Brak reguł walidacji dla metody DELETE.",
 *             example={}
 *         )
 *     )
 * )
 */
class WorktimeRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'date' => 'array|date',
            'date.*' => 'date',
        ],
        self::METHOD_POST => [
        ],
        self::METHOD_PUT => [
        ],
        self::METHOD_DELETE => [
        ],
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = self::$rules[$this->getMethod()] ?? [];

        return $rules;
    }
}
