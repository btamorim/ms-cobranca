<?php

namespace App\DTO;

final class TicketDTO {
    public function __construct(
        public readonly int $debtId,
        public readonly string $governmentId,
        public readonly float $amount,
        public readonly string $debtDueDate,
        public readonly string $barCode,
        public readonly int $bankId,
        public readonly int $customerId,
    ){}

    public function toArray():array
    {
        return [
            'debtId' => $this->debtId,
            'governmentId' => $this->governmentId,
            'amount' => $this->amount,
            'debtDueDate' => $this->debtDueDate,
            'bankId' => $this->bankId,
            'barCode' => $this->barCode,
            'customerId' => $this->customerId,
            'status' => false
        ];
    }
}
