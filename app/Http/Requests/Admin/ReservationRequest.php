<?php

namespace App\Http\Requests\Admin;

use App\Enum\ReservationStatus;
use App\Http\Requests\RequestTrait;
use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        'admin.reservations.store' => [
            'table_id' => 'nullable|integer|exists:tenant.tables,id|required_unless:status,2',
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|min:3|max:100',
            'people_number' => 'required|integer|min:1|max:100',
            'start' => 'required|date',
            //'end' => 'nullable|date|after:start',
            'phone' => 'nullable|string|min:11|max:11',
            'description' => 'nullable|string|min:3|max:1000',
            'active' => 'boolean',
            'closed' => 'boolean',
            'status' => 'required|integer|min:0|max:2',
        ],
        'admin.reservations.update' => [
            'table_id' => 'nullable|integer|exists:tenant.tables,id|required_unless:status,2',
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|min:3|max:100',
            'people_number' => 'required|integer|min:1|max:100',
            'start' => 'required|date',
            //'end' => 'nullable|date|after:start',
            'phone' => 'nullable|string|min:11|max:11',
            'description' => 'nullable|string|min:3|max:1000',
            'active' => 'boolean',
            'closed' => 'boolean',
            'status' => 'required|integer|min:0|max:2',
        ],
    ];

    public function attributes()
    {
        return [
            'table_id' => __('validation.reservation.table'),
            'user_id' => __('validation.reservation.user'),
            'name' => __('validation.reservation.name'),
            'people_number' => __('validation.reservation.people_number'),
            'start' => __('validation.reservation.start'),
            // 'end' => __('validation.reservation.end'),
            'phone' => __('validation.reservation.phone'),
            'description' => __('validation.reservation.description'),
            'active' => __('validation.reservation.active'),
            'closed' => __('validation.reservation.closed'),
            'status' => __('validation.reservation.status'),
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
        $isStatusCanceled = ReservationStatus::from($this->input('status')) == ReservationStatus::CANCELLED;
        $isTable = $this->input('table_id') !== null;

        if (! $isStatusCanceled && $isTable) {
            self::$rules['admin.reservations.store']['table_id'] .= '|required';
            self::$rules['admin.reservations.store']['people_number'] .= sprintf('|lte:%s', Table::find($this->table_id)->people_number);
            self::$rules['admin.reservations.update']['people_number'] .= sprintf('|lte:%s', Table::find($this->table_id)->people_number);
        }

        return self::$rules[$this->route()->getName()] ?? [];
    }
}
