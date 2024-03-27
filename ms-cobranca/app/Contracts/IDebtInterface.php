<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

interface IDebtInterface
{
    public function updateDebt(int $debId, int $ticketId): bool;
}
