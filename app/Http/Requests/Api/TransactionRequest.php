<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="TransactionRequestGET",
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
 *         description="Tablica identyfikatorów transakcji. Każdy identyfikator musi być liczbą całkowitą i co najmniej 1."
 *     ),
 *     @OA\Property(
 *         property="order",
 *         type="object",
 *         @OA\Property(
 *             property="id",
 *             type="string",
 *             example="asc",
 *             description="Kierunek sortowania po identyfikatorze transakcji. Musi być 'asc' lub 'desc'."
 *         ),
 *         @OA\Property(
 *             property="amount",
 *             type="string",
 *             example="desc",
 *             description="Kierunek sortowania po kwocie transakcji. Musi być 'asc' lub 'desc'."
 *         ),
 *         @OA\Property(
 *             property="date",
 *             type="string",
 *             example="asc",
 *             description="Kierunek sortowania po dacie transakcji. Musi być 'asc' lub 'desc'."
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TransactionRequestPOST",
 *     type="object",
 *     @OA\Property(
 *         property="amount",
 *         type="number",
 *         format="float",
 *         example=100.00,
 *         description="Kwota transakcji. To pole jest wymagane."
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date",
 *         example="2024-07-31",
 *         description="Data transakcji. To pole jest wymagane."
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Opis transakcji",
 *         description="Opis transakcji. Opcjonalne pole."
 *     ),
 *     required={"amount", "date"}
 * )
 *
 * @OA\Schema(
 *     schema="TransactionRequestPUT",
 *     type="object",
 *     @OA\Property(
 *         property="amount",
 *         type="number",
 *         format="float",
 *         example=150.00,
 *         description="Kwota transakcji. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date",
 *         example="2024-07-31",
 *         description="Data transakcji. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Zaktualizowany opis transakcji",
 *         description="Opis transakcji. Opcjonalne pole."
 *     ),
 *     required={"id"}
 * )
 *
 * @OA\Schema(
 *     schema="TransactionRequestDELETE",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Identyfikator transakcji do usunięcia. To pole jest wymagane i musi być liczbą całkowitą oraz co najmniej 1."
 *     ),
 *     required={"id"}
 * )
 */
class TransactionRequest extends FormRequest
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
