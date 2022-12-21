<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface IUploadInterface
{
    public function storeFile(Request $request): bool;
}