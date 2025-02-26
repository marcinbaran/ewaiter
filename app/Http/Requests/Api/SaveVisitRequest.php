<?php

namespace App\Http\Requests\Api;

use App\Enum\VisitObject;
use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="SaveVisitRequest",
 *     type="object",
 *     required={"visit_object_type"},
 *     @OA\Property(property="mac_address", type="string", description="MAC address of the visit", example="00:1A:2B:3C:4D:5E"),
 *     @OA\Property(property="visit_object_type", type="string", enum={"type1", "type2"}, description="Type of the visit object", example="type1"),
 *     @OA\Property(property="visit_object_name", type="string", description="Name of the visit object", example="Example Object"),
 *     @OA\Property(property="visit_object_id", type="integer", description="ID of the visit object", example=123),
 * )
 */
class SaveVisitRequest extends FormRequest
{
    use RequestTrait;

    public const string MAC_ADDRESS_PARAM_KEY = 'mac_address';

    public const string VISIT_OBJECT_TYPE_PARAM_KEY = 'visit_object_type';

    public const string VISIT_OBJECT_NAME_PARAM_KEY = 'visit_object_name';

    public const string VISIT_OBJECT_ID_PARAM_KEY = 'visit_object_id';

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
        return [
            self::VISIT_OBJECT_TYPE_PARAM_KEY => 'required|string|in:' . VisitObject::getValuesForRequestRule(),
            self::VISIT_OBJECT_NAME_PARAM_KEY => 'nullable|string|min:1',
            self::VISIT_OBJECT_ID_PARAM_KEY => 'nullable|int|min:0',
            self::MAC_ADDRESS_PARAM_KEY => 'nullable|string|min:1',
        ];
    }
}
