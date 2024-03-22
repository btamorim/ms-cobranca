<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface IDebtRepositoryInterface
{
    public function update(int $debId, int $ticketId): bool;
}
