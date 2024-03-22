<?php

namespace App\Repository;

use App\DTO\TicketDTO;
use App\Models\Ticket;
use App\Contracts\ITicketRepositoryInterface;

class TicketRepository implements ITicketRepositoryInterface
{
    public function save(TicketDTO $ticketDTO): Ticket
    {
        /**
         * simulate the insert in BD
         */
        //$ticket = Ticket::create($ticketDTO->toArray());

        $ticket = new Ticket();
        $ticket->ticketId = rand(1,99999);

        return $ticket;
    }

    public function update(int $ticketId, array $data): bool
    {
        /**
         * simulate the insert in BD
         */
        //$ticket = Ticket::where('ticketId', $ticketId)->update($data);

        return true;
    }

    public function findByDebtId(int $debitId): Ticket
    {
        /**
         * simulate the insert in BD
         */
        // $ticket = Ticket::where('debitId', $debtId)->firstOrFail();

        $ticket = new Ticket();
        $ticket->ticketId = rand(1,99999);

        return $ticket;
    }
}
