<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="TableRequestGET",
 *     type="object",
 *     @OA\Property(
 *         property="itemsPerPage",
 *         type="integer",
 *         example=10,
 *         description="Liczba elementów na stronę. Musi być między 1 a 50."
 *     ),
 *     @OA\Property(
 *         property="page",
 *         type="integer",
 *         example=1,
 *         description="Numer strony. Musi być co najmniej 1."
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="array",
 *         @OA\Items(
 *             type="integer",
 *             example=1
 *         ),
 *         description="Tablica identyfikatorów tabel. Każdy identyfikator musi być liczbą całkowitą i co najmniej 1."
 *     ),
 *     @OA\Property(
 *         property="withOrders",
 *         type="boolean",
 *         example=true,
 *         description="Flaga wskazująca, czy uwzględniać zamówienia."
 *     ),
 *     @OA\Property(
 *         property="order",
 *         type="object",
 *         @OA\Property(
 *             property="id",
 *             type="string",
 *             example="asc",
 *             description="Kierunek sortowania po identyfikatorze tabeli. Musi być 'asc' lub 'desc'."
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="desc",
 *             description="Kierunek sortowania po nazwie tabeli. Musi być 'asc' lub 'desc'."
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TableRequestPOST",
 *     type="object",
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Opis tabeli",
 *         description="Opis tabeli. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="people_number",
 *         type="integer",
 *         example=4,
 *         description="Liczba osób, które tabela może pomieścić. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="active",
 *         type="boolean",
 *         example=true,
 *         description="Flaga wskazująca, czy tabela jest aktywna."
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Stół 1",
 *         description="Nazwa tabeli. To pole jest wymagane i musi mieć maksymalnie 255 znaków."
 *     ),
 *     required={"name"}
 * )
 *
 * @OA\Schema(
 *     schema="TableRequestPUT",
 *     type="object",
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Zaktualizowany opis tabeli",
 *         description="Opis tabeli. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="people_number",
 *         type="integer",
 *         example=6,
 *         description="Liczba osób, które tabela może pomieścić. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="active",
 *         type="boolean",
 *         example=false,
 *         description="Flaga wskazująca, czy tabela jest aktywna."
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Zaktualizowany stół",
 *         description="Nazwa tabeli. Opcjonalne pole, ale jeśli jest obecne, musi mieć maksymalnie 255 znaków."
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TableRequestDELETE",
 *     type="object",
 *     description="Schemat żądania dla metody DELETE w TableRequest. Brak określonych reguł walidacji."
 * )
 */
class TableRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'id' => 'array|min:1',
            'id.*' => 'integer|min:1',
            'withOrders' => 'boolean',
            'order.id' => 'string|in:asc,desc',
            'order.name' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'description' => 'nullable|string',
            'people_number' => 'nullable|integer',
            'active' => 'boolean',
            'name' => 'required|string|max:255',
        ],
        self::METHOD_PUT => [
            'description' => 'nullable|string',
            'people_number' => 'nullable|integer',
            'active' => 'boolean',
            'name' => 'string|max:255',
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
        return self::$rules[$this->getMethod()] ?? [];
    }
}
