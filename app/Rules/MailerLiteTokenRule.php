<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use MailerLite\Exceptions\MailerLiteException;
use MailerLite\MailerLite;

class MailerLiteTokenRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $client = new MailerLite(['api_key' => $value]);

        try {
            $client->subscribers->get(['limit' => 1]);
        } catch (MailerLiteException) {
            $fail('Please enter valid :attribute');
        }

    }
}
