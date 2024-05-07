<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PermissionCodeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $splitValue = explode('-', $value);
        $system = $splitValue[0];
        $subSystem = $splitValue[1];
        $module = $splitValue[2];
        $action = $splitValue[3];
        if($system == 'null' || $subSystem == 'null' || $module == 'null' || $action == 'null') {
            $fail(__('The :attribute is invalid'));
        }
    }
}
