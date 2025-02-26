<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\Room\MaxNumberForBatchCreatingRooms;
use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.rooms.store' => [
            'adding_type' => 'required|string|in:1,2',
            'name' => 'required_if:adding_type,single|nullable|string|min:3|max:100',
            'number' => 'required_if:adding_type,single|nullable|string|min:1|max:100',
            'from_number' => 'required_if:adding_type,range|nullable|integer|min:1|max:10000|lte:to_number',
            'to_number' => [
                'required_if:adding_type,2',
                'nullable',
                'integer',
                'min:1',
                'max:10000',
                'gte:from_number',
            ],
            //'to_number' => 'required_if:adding_type,range|nullable|integer|min:1|max:10000|gte:from_number',
            'floor' => 'nullable|integer|min:-100|max:100',
        ],
        'admin.rooms.update' => [
            'name' => 'required|string|min:3|max:100',
            'number' => 'required|string|min:1|max:100',
            'floor' => 'nullable|integer|min:-100|max:100',
        ],
    ];

    public function attributes()
    {
        return [
            'adding_type' => __('validation.room.adding_type'),
            'name' => __('validation.room.name'),
            'number' => __('validation.room.number'),
            'from_number' => __('validation.room.from_number'),
            'to_number' => __('validation.room.to_number'),
            'floor' => __('validation.room.floor'),
        ];
    }

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
        $rules = self::$rules[$this->route()->getName()] ?? [];
        $rules['to_number'][] = new MaxNumberForBatchCreatingRooms();

        return $rules;
    }
}
