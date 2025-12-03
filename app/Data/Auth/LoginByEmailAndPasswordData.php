<?php

namespace App\Data\Auth;

final readonly class LoginByEmailAndPasswordData
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}


