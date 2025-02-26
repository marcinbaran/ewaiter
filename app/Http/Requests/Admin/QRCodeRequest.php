<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\QRCode\UniqueObjectRoom;
use App\Rules\QRCode\UniqueObjectTable;
use Illuminate\Foundation\Http\FormRequest;

class QRCodeRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private array $rules = [
        'admin.qr_codes.store' => [
            'object_id_table' => ['required_if:object_type,table', 'integer'],
            'object_id_room' => ['required_if:object_type,room', 'integer'],
            'object_type' => 'required|string',
            'redirect' => 'boolean',
        ],
        'admin.qr_codes.update' => [
            'object_id_table' => 'required_if:object_type,table|integer',
            'object_id_room' => 'required_if:object_type,room|integer',
            'object_type' => 'required|string',
            'redirect' => 'boolean',
        ],
    ];

    public function attributes()
    {
        return [
            'object_id_table' => __('validation.qr_code.table'),
            'object_id_room' => __('validation.qr_code.room'),
            'object_type' => __('validation.qr_code.type'),
            'redirect' => __('validation.qr_code.redirect'),
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
        $objectType = $this->input('object_type');

        if ($this->route()->getName() === 'admin.qr_codes.store' && $objectType == 'table') {
            $this->rules['admin.qr_codes.store']['object_id_table'][] = new UniqueObjectTable($objectType);
        } elseif ($this->route()->getName() === 'admin.qr_codes.store' && $objectType == 'room') {
            $this->rules['admin.qr_codes.store']['object_id_room'][] = new UniqueObjectRoom($objectType);
        }

        return $this->rules[$this->route()->getName()] ?? [];
    }
}
