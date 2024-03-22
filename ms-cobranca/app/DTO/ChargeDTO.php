<?php

namespace App\DTO;

final class ChargeDTO {
    public function __construct(
        public readonly string $name,
        public readonly string $governmentId,
        public readonly string $email,
        public readonly float $debtAmount,
        public readonly string $debtDueDate,
        public readonly int $debtId
    ){}

    public function toArray():array
    {
        return [
            'name' => $this->name,
            'governmentId' => $this->governmentId,
            'email' => $this->email,
            'debtAmount' => $this->debtAmount,
            'debtDueDate' => $this->debtDueDate,
            'debtId' => $this->debtId
        ];
    }
}
