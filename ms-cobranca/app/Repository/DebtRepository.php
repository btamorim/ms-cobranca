<?php

namespace App\Repository;

use App\Contracts\IDebtRepositoryInterface;

class DebtRepository implements IDebtRepositoryInterface
{
    public function update(int $debId, int $ticketId): bool
    {
        /**
         * simulate the update in BD
         */
        // Debt::where(['debtId' => $debId])->update([
            //     'ticketId' => $ticketId,
            //     'status' => 1
            // ]);
        return true;
    }
}
