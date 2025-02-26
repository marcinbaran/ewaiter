<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="VersionRequest",
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
 *                 property="example",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 description="Przykłady wartości, jeśli są wymagane.",
 *                 example={}
 *             )
 *         )
 *     )
 * )
 */
class VersionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            self::METHOD_GET => [
                'platform' => 'required|string',
                'version' => 'required|string',
            ]
        ];

        return $rules[$this->getMethod()];
    }
}
