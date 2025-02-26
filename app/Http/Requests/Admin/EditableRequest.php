<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class EditableRequest extends FormRequest
{
    use RequestTrait;

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
            'model' => 'required|string|min:3',
            'id' => 'required|int',
            'column' => 'required|string',
            'value' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'model' => __('validation.editable.model'),
            'id' => __('validation.editable.id'),
            'column' => __('validation.editable.column'),
            'value' => __('validation.editable.value'),
        ];
    }
}
