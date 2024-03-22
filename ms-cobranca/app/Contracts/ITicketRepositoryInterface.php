<?php

namespace App\Contracts;

use App\DTO\TicketDTO;
use App\Models\Ticket;
use Illuminate\Http\Request;

interface ITicketRepositoryInterface
{
    public function save(TicketDTO $ticketDTO): Ticket;

    public function update(int $ticketId, array $data): bool;

    public function findByDebtId(int $debitId): Ticket;
}
