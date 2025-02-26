<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="VoucherRequest",
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
 *                 property="itemsPerPage",
 *                 type="integer",
 *                 format="int32",
 *                 description="Liczba elementów na stronę.",
 *                 example=10
 *             ),
 *             @OA\Property(
 *                 property="page",
 *                 type="integer",
 *                 format="int32",
 *                 description="Numer strony.",
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="id",
 *                 type="array",
 *                 @OA\Items(type="integer", format="int32"),
 *                 description="Lista identyfikatorów voucherów.",
 *                 example={1, 2, 3}
 *             ),
 *             @OA\Property(
 *                 property="order.id",
 *                 type="string",
 *                 description="Sortowanie po identyfikatorze.",
 *                 example="asc"
 *             ),
 *             @OA\Property(
 *                 property="order.name",
 *                 type="string",
 *                 description="Sortowanie po nazwie.",
 *                 example="desc"
 *             )
 *         ),
 *         @OA\Property(
 *             property="POST",
 *             type="object",
 *             description="Reguły walidacji dla metody POST.",
 *             @OA\Property(
 *                 property="voucher",
 *                 type="string",
 *                 description="Kod voucheru.",
 *                 example="SAVE10"
 *             )
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
class VoucherRequest extends FormRequest
{
    use RequestTrait;

    public const string VOUCHER_PARAM_KEY = 'voucher';

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
        ],
        self::METHOD_POST => [
            self::VOUCHER_PARAM_KEY => 'required|string|max:255',
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
