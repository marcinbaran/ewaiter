<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\RequestTrait;
use App\Rules\Worktime\SingleWorktime;
use Illuminate\Foundation\Http\FormRequest;
use Request;

class WorktimeRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private array $rules = [
        'admin.worktimes.store' => [
            'type' => 'integer|in:0,1',
            'date' => ['required', 'date'],
            'start' => 'date_format:H:i|before:end',
            'end' => 'date_format:H:i|after:start',
            'visibility' => 'boolean',
        ],
        'admin.worktimes.update' => [
            'type' => 'integer|in:0,1',
            'date' => ['required', 'date'],
            'start' => 'date_format:H:i|before:end',
            'end' => 'date_format:H:i|after:start',
            'visibility' => 'boolean',
        ],
    ];

    public function attributes()
    {
        return [
            'type' => __('validation.worktime.type'),
            'date' => __('validation.worktime.date'),
            'start' => __('validation.worktime.start'),
            'end' => __('validation.worktime.end'),
            'visibility' => __('validation.worktime.visibility'),
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
        $this->rules['admin.worktimes.store']['date'][] = new SingleWorktime();

        return $this->rules[$this->route()->getName()] ?? [];
    }
}
