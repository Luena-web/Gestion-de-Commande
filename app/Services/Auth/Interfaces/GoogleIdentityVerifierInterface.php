<?php

namespace App\Services\Auth\Interfaces;

use App\DTO\Auth\GoogleIdentityDTO;

interface GoogleIdentityVerifierInterface
{
    public function verify(string $credential): GoogleIdentityDTO;
}
