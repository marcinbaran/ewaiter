<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use App\Rules\User\SetPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\ResponseTrait;
/**
 * @OA\Schema(
 *     schema="PhoneRequest_GET",
 *     type="object",
 *     required={"phone"},
 *     @OA\Property(property="phone", type="string", maxLength=20, description="Unique phone number")
 * )
 *
 * @OA\Schema(
 *     schema="PhoneRequest_POST",
 *     type="object",
 *     required={"phone"},
 *     @OA\Property(property="phone", type="string", maxLength=20, description="Unique phone number")
 * )
 *
 * @OA\Schema(
 *     schema="PhoneRequest_PUT",
 *     type="object"
 * )
 *
 * @OA\Schema(
 *     schema="PhoneRequest_DELETE",
 *     type="object"
 * )
 */
class PhoneRequest extends FormRequest
{
  use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'phone' => 'required|string|unique:users,phone|max:20',
        ],
        self::METHOD_POST => [
            'phone' => 'required|string|unique:users,phone|max:20',
        ],
        self::METHOD_PUT => [
            //
        ],
        self::METHOD_DELETE => [
            //
        ],
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            self::METHOD_GET => [
            ],
            self::METHOD_POST => [
                'phone' => new SetPhoneRule,
            ],
            self::METHOD_PUT => [
            ],
            self::METHOD_DELETE => [
            ],
        ];

        return $rules[$this->getMethod()] ?? [];
    }
}
