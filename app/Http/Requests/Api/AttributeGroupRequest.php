<?php

namespace App\Http\Requests\Api;

use App\Enum\AttributeGroupInputType;
use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="AttributeGroup_GET",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID of the attribute group"),
 *     @OA\Property(property="with_attributes", type="boolean", description="Include attributes in the response")
 * )
 *
 * @OA\Schema(
 *     schema="AttributeGroup_POST",
 *     type="object",
 *     required={"key", "name", "input_type", "is_primary", "is_active"},
 *     @OA\Property(property="key", type="string", maxLength=255, description="Unique key of the attribute group"),
 *     @OA\Property(property="name", type="array", description="Name translations of the attribute group",
 *         @OA\Items(type="string", maxLength=50, description="Name translation")
 *     ),
 *     @OA\Property(property="description", type="array", description="Description translations of the attribute group",
 *         @OA\Items(type="string", maxLength=1000, description="Description translation")
 *     ),
 *     @OA\Property(property="input_type", type="string", enum={"text", "number", "date"}, description="Input type"),
 *     @OA\Property(property="is_primary", type="boolean", description="Is primary attribute group"),
 *     @OA\Property(property="is_active", type="boolean", description="Is the attribute group active")
 * )
 *
 * @OA\Schema(
 *     schema="AttributeGroup_PUT",
 *     type="object",
 *     required={"key", "name", "input_type", "is_primary", "is_active"},
 *     @OA\Property(property="key", type="string", maxLength=255, description="Unique key of the attribute group"),
 *     @OA\Property(property="name", type="array", description="Name translations of the attribute group",
 *         @OA\Items(type="string", maxLength=50, description="Name translation")
 *     ),
 *     @OA\Property(property="description", type="array", description="Description translations of the attribute group",
 *         @OA\Items(type="string", maxLength=1000, description="Description translation")
 *     ),
 *     @OA\Property(property="input_type", type="string", enum={"text", "number", "date"}, description="Input type"),
 *     @OA\Property(property="is_primary", type="boolean", description="Is primary attribute group"),
 *     @OA\Property(property="is_active", type="boolean", description="Is the attribute group active")
 * )
 *
 * @OA\Schema(
 *     schema="AttributeGroup_DELETE",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID of the attribute group")
 * )
 */
class AttributeGroupRequest extends FormRequest
{
    use RequestTrait;

    public const string ID_KEY = 'id';

    public const string KEY_KEY = 'key';

    public const string NAME_KEY = 'name';

    public const string DESCRIPTION_KEY = 'description';

    public const string INPUT_TYPE_KEY = 'input_type';

    public const string IS_PRIMARY_KEY = 'is_primary';

    public const string IS_ACTIVE_KEY = 'is_active';

    public const string WITH_ATTRIBUTES_KEY = 'with_attributes';

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
        $rules = [
            self::METHOD_GET => [
                self::ID_KEY => 'nullable|int',
                self::WITH_ATTRIBUTES_KEY => 'nullable|boolean',
            ],
            self::METHOD_POST => [
                self::KEY_KEY => 'required|string|max:255|unique:tenant.attribute_groups,key',
                self::NAME_KEY => 'required|array|min:1|max:30',
                self::NAME_KEY.'.*' => 'nullable|string|min:3|max:50',
                self::NAME_KEY.'.pl' => 'required|string|min:3|max:50',
                self::DESCRIPTION_KEY => 'nullable|array|min:1|max:30',
                self::DESCRIPTION_KEY.'.*' => 'nullable|string|min:3|max:1000',
                self::INPUT_TYPE_KEY => 'required|string|in:'.AttributeGroupInputType::getValuesForRequestRule(),
                self::IS_PRIMARY_KEY => 'required|boolean',
                self::IS_ACTIVE_KEY => 'required|boolean',
            ],
            self::METHOD_PUT => [
                self::KEY_KEY => 'required|string|max:255|unique:tenant.attribute_groups,key',
                self::NAME_KEY => 'required|array|min:1|max:30',
                self::NAME_KEY.'.*' => 'nullable|string|min:3|max:50',
                self::NAME_KEY.'.pl' => 'required|string|min:3|max:50',
                self::DESCRIPTION_KEY => 'nullable|array|min:1|max:30',
                self::DESCRIPTION_KEY.'.*' => 'nullable|string|min:3|max:1000',
                self::INPUT_TYPE_KEY => 'required|string|in:'.AttributeGroupInputType::getValuesForRequestRule(),
                self::IS_PRIMARY_KEY => 'required|boolean',
                self::IS_ACTIVE_KEY => 'required|boolean',
            ],
            self::METHOD_DELETE => [
                self::ID_KEY => 'required|int',
            ],
        ];

        return $rules[$this->getMethod()];
    }
}
