<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

interface IUploadInterface
{
    public function storeFile(UploadedFile $request): bool;
}