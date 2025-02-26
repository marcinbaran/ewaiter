<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="UserAuthRequestGET",
 *     type="object",
 *     @OA\Property(
 *         property="auth_code",
 *         type="string",
 *         description="Kod autoryzacyjny (jeśli potrzebny dla GET). To pole jest opcjonalne."
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UserAuthRequestPOST",
 *     type="object",
 *     @OA\Property(
 *         property="auth_code",
 *         type="string",
 *         example="123456",
 *         description="Kod autoryzacyjny. To pole jest wymagane i musi mieć maksymalnie 20 znaków."
 *     ),
 *     required={"auth_code"}
 * )
 *
 * @OA\Schema(
 *     schema="UserAuthRequestPUT",
 *     type="object",
 *     @OA\Property(
 *         property="auth_code",
 *         type="string",
 *         description="Kod autoryzacyjny (jeśli potrzebny dla PUT). To pole jest opcjonalne."
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UserAuthRequestDELETE",
 *     type="object",
 *     @OA\Property(
 *         property="auth_code",
 *         type="string",
 *         description="Kod autoryzacyjny (jeśli potrzebny dla DELETE). To pole jest opcjonalne."
 *     )
 * )
 */

class UserAuthRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
        ],
        self::METHOD_POST => [
            'auth_code' => 'required|max:20',
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
