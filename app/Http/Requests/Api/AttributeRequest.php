<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="Attribute_GET",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID of the attribute"),
 *     @OA\Property(property="with_attribute_group", type="boolean", description="Include attribute group in the response")
 * )
 *
 * @OA\Schema(
 *     schema="Attribute_POST",
 *     type="object",
 *     required={"key", "name", "is_active"},
 *     @OA\Property(property="key", type="string", maxLength=255, description="Unique key of the attribute"),
 *     @OA\Property(property="name", type="array", description="Name translations of the attribute",
 *         @OA\Items(type="string", maxLength=50, description="Name translation")
 *     ),
 *     @OA\Property(property="description", type="array", description="Description translations of the attribute",
 *         @OA\Items(type="string", maxLength=1000, description="Description translation")
 *     ),
 *     @OA\Property(property="icon", type="string", maxLength=255, description="Icon of the attribute"),
 *     @OA\Property(property="is_active", type="boolean", description="Is the attribute active"),
 *     @OA\Property(property="attribute_group_id", type="integer", description="ID of the attribute group"),
 * )
 *
 * @OA\Schema(
 *     schema="Attribute_PUT",
 *     type="object",
 *     required={"key", "name", "is_active"},
 *     @OA\Property(property="key", type="string", maxLength=255, description="Unique key of the attribute"),
 *     @OA\Property(property="name", type="array", description="Name translations of the attribute",
 *         @OA\Items(type="string", maxLength=50, description="Name translation")
 *     ),
 *     @OA\Property(property="description", type="array", description="Description translations of the attribute",
 *         @OA\Items(type="string", maxLength=1000, description="Description translation")
 *     ),
 *     @OA\Property(property="icon", type="string", maxLength=255, description="Icon of the attribute"),
 *     @OA\Property(property="is_active", type="boolean", description="Is the attribute active"),
 *     @OA\Property(property="attribute_group_id", type="integer", description="ID of the attribute group"),
 * )
 *
 * @OA\Schema(
 *     schema="Attribute_DELETE",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID of the attribute")
 * )
 */
class AttributeRequest extends FormRequest
{
    use RequestTrait;

    public const string ID_KEY = 'id';

    public const string KEY_KEY = 'key';

    public const string NAME_KEY = 'name';

    public const string DESCRIPTION_KEY = 'description';

    public const string ICON_KEY = 'icon';

    public const string IS_ACTIVE_KEY = 'is_active';

    public const string ATTRIBUTE_GROUP_ID_KEY = 'attribute_group_id';

    public const string WITH_ATTRIBUTE_GROUP_KEY = 'with_attribute_group';

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
                self::WITH_ATTRIBUTE_GROUP_KEY => 'nullable|boolean',
            ],
            self::METHOD_POST => [
                self::KEY_KEY => 'required|string|max:255|unique:tenant.attributes,key',
                self::NAME_KEY => 'required|array|min:1|max:30',
                self::NAME_KEY.'.*' => 'nullable|string|min:3|max:50',
                self::NAME_KEY.'.pl' => 'required|string|min:3|max:50',
                self::DESCRIPTION_KEY => 'nullable|array|min:1|max:30',
                self::DESCRIPTION_KEY.'.*' => 'nullable|string|min:3|max:1000',
                self::ICON_KEY => 'nullable|string|max:255',
                self::IS_ACTIVE_KEY => 'required|boolean',
                self::ATTRIBUTE_GROUP_ID_KEY => 'nullable|int',
            ],
            self::METHOD_PUT => [
                self::KEY_KEY => 'required|string|max:255|unique:tenant.attributes,key',
                self::NAME_KEY => 'required|array|min:1|max:30',
                self::NAME_KEY.'.*' => 'nullable|string|min:3|max:50',
                self::NAME_KEY.'.pl' => 'required|string|min:3|max:50',
                self::DESCRIPTION_KEY => 'nullable|array|min:1|max:30',
                self::DESCRIPTION_KEY.'.*' => 'nullable|string|min:3|max:1000',
                self::ICON_KEY => 'nullable|string|max:255',
                self::IS_ACTIVE_KEY => 'required|boolean',
                self::ATTRIBUTE_GROUP_ID_KEY => 'nullable|int',
            ],
            self::METHOD_DELETE => [
                self::ID_KEY => 'required|int',
            ],
        ];

        return $rules[$this->getMethod()];
    }
}
