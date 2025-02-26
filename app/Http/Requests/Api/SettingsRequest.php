<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="SettingsRequestGET",
 *     type="object",
 *     @OA\Property(
 *         property="itemsPerPage",
 *         type="integer",
 *         example=10,
 *         description="Number of items per page. Must be between 1 and 50."
 *     ),
 *     @OA\Property(
 *         property="page",
 *         type="integer",
 *         example=1,
 *         description="Page number. Must be at least 1."
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="array",
 *         @OA\Items(
 *             type="string",
 *             example="setting_id_123",
 *             description="List of setting IDs."
 *         ),
 *         description="Array of setting IDs. Each ID must be a string and at least 1 character long."
 *     ),
 *     description="Request schema for the GET method of SettingsRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="SettingsRequestPOST",
 *     type="object",
 *     description="Request schema for the POST method of SettingsRequest. Currently, no specific rules defined."
 * )
 */
/**
 * @OA\Schema(
 *     schema="SettingsRequestPUT",
 *     type="object",
 *     description="Request schema for the PUT method of SettingsRequest. Currently, no specific rules defined."
 * )
 */
/**
 * @OA\Schema(
 *     schema="SettingsRequestDELETE",
 *     type="object",
 *     description="Request schema for the DELETE method of SettingsRequest. Currently, no specific rules defined."
 * )
 */

class SettingsRequest extends FormRequest
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
            'id.*' => 'string|min:1',
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
        if ('api.manage_settings.close_restaurant' == $this->route()->getName()) {
            $rules['close'] = 'required|boolean';
        }
        if ('api.manage_settings.address_delivery' == $this->route()->getName()) {
            $rules['disable'] = 'required|boolean';
        }

        return $rules;
    }
}
