<?php

namespace App\Rules\Table;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxNumberForBatchCreatingTables implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    // ...

    /**
     * Set the data under validation.
     *
     * @param array<string, mixed> $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->data['adding_type'] == 'range') {
            $from = $this->data['from_number'];
            $to = $this->data['to_number'];
            $number = $to - $from;

            if ($number > 50) {
                $fail(__('validation.max_number_for_batch_creating_tables', ['number' => 50]));
            }
        }
    }
}
