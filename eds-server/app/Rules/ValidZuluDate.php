<?php

namespace App\Rules;

use App\Enums\AppConstants;
use Illuminate\Contracts\Validation\Rule;

class ValidZuluDate implements Rule
{
    /**
     * Create a new rule instance.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $date = \DateTime::createFromFormat(AppConstants::ZULU_DATE_FORMAT, $value);

        return $date !== false;
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message()
    {
        return 'The :attribute date format should be: ' . AppConstants::ZULU_DATE_FORMAT;
    }
}
