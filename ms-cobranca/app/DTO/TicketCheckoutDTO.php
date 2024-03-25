<?php

namespace App\DTO;

use Carbon\Carbon;

final class TicketCheckoutDTO {
    public function __construct(
        public readonly int $debtId,
        public readonly float $paidAmount,
        public readonly Carbon $paidAt,
        public readonly string $paidBy,
    ){}

    public function toArray():array
    {
        return [
            'debtId' => $this->debtId,
            'paidAmount' => $this->paidAmount,
            'paidAt' => $this->paidAt,
            'paidBy' => $this->paidBy,
        ];
    }
}
