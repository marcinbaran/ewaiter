<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="TrWithdrawRequestGET",
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
 *         description="Tablica identyfikatorów wypłat. Każdy identyfikator musi być liczbą całkowitą i co najmniej 1."
 *     ),
 *     @OA\Property(
 *         property="order",
 *         type="object",
 *         @OA\Property(
 *             property="id",
 *             type="string",
 *             example="asc",
 *             description="Kierunek sortowania po identyfikatorze wypłaty. Musi być 'asc' lub 'desc'."
 *         ),
 *         @OA\Property(
 *             property="amount",
 *             type="string",
 *             example="desc",
 *             description="Kierunek sortowania po kwocie wypłaty. Musi być 'asc' lub 'desc'."
 *         ),
 *         @OA\Property(
 *             property="date",
 *             type="string",
 *             example="asc",
 *             description="Kierunek sortowania po dacie wypłaty. Musi być 'asc' lub 'desc'."
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TrWithdrawRequestPOST",
 *     type="object",
 *     @OA\Property(
 *         property="amount",
 *         type="number",
 *         format="float",
 *         example=150.00,
 *         description="Kwota wypłaty. To pole jest wymagane i musi być liczbą nieujemną."
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date",
 *         example="2024-07-31",
 *         description="Data wypłaty. To pole jest wymagane."
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Opis wypłaty",
 *         description="Opis wypłaty. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         example="pending",
 *         description="Status wypłaty. Musi być jednym z: 'pending', 'completed', 'cancelled'. To pole jest wymagane."
 *     ),
 *     required={"amount", "date", "status"}
 * )
 *
 * @OA\Schema(
 *     schema="TrWithdrawRequestPUT",
 *     type="object",
 *     @OA\Property(
 *         property="amount",
 *         type="number",
 *         format="float",
 *         example=200.00,
 *         description="Kwota wypłaty. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date",
 *         example="2024-08-01",
 *         description="Data wypłaty. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Zaktualizowany opis wypłaty",
 *         description="Opis wypłaty. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         example="completed",
 *         description="Status wypłaty. Musi być jednym z: 'pending', 'completed', 'cancelled'. Opcjonalne pole."
 *     ),
 *     required={"id"}
 * )
 *
 * @OA\Schema(
 *     schema="TrWithdrawRequestDELETE",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Identyfikator wypłaty do usunięcia. To pole jest wymagane i musi być liczbą całkowitą oraz co najmniej 1."
 *     ),
 *     required={"id"}
 * )
 */
class TrWithdrawRequest extends FormRequest
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
            'order.id' => 'string|in:asc,desc',
            'order.name' => 'string|in:asc,desc',
            'order.tag' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'name' => 'required|string|max:255',
            'tag' => 'required|string|max:255|unique:tenant.tags,tag',
            'description' => 'nullable|string',
            'icon' => 'nullable',
            'visibility' => 'boolean',
        ],
        self::METHOD_PUT => [
            'name' => 'required|string|max:255',
            'tag' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable',
            'visibility' => 'boolean',
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
        if ('PUT' == $this->getMethod()) {
            $rules['tag'][] = Rule::unique('tags', 'tag')->ignore($this->id);
        }

        return self::$rules[$this->getMethod()] ?? [];
    }
}
