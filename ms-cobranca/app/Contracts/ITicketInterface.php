<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use App\DTO\TicketCheckoutDTO;

interface ITicketInterface
{
    public function checkoutDebit(): bool;

    public function checkoutTicket(TicketCheckoutDTO $ticketCheckoutDTO): bool;
}
