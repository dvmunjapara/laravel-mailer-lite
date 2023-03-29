<?php

namespace App\Services;

use App\Models\User;
use MailerLite\Exceptions\MailerLiteException;
use MailerLite\MailerLite;

class MailerLiteService
{
    public function getClient()
    {
        $user = User::firstWhere('email', request()->header('email'));
        $token = $user->token;

        if (empty($token)) {

            throw new MailerLiteException("Token is missing");
        }

        return new MailerLite(['api_key' => $token]);
    }
}
