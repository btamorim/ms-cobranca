<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface ITicketInterface
{
    public function checkoutDebit(): bool;

    public function checkoutTicket(Request $request);

}
