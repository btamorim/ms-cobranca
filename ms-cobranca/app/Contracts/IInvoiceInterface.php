<?php

namespace App\Contracts;

use App\DTO\ChargeDTO;
use Illuminate\Http\Request;

interface IInvoiceInterface
{
    public function generateInvoice(ChargeDTO $chargeDTO): array;
}
