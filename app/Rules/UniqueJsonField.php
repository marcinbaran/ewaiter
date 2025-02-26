<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueJsonField implements ValidationRule
{
    protected $table;

    protected $column;

    protected $ignoreId;

    public function __construct(string $table, string $column, int $ignoreId = 0)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::connection('tenant')->table($this->table)
            ->where($this->column, 'like', '%"'.$value.'"%');

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail(__('validation.name_not_unique'));
        }
    }

    public function message()
    {
        return 'The :attribute is not unique.';
    }
}
