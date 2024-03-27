<?php

namespace App\Services\Debt;

use App\Models\Debt;
use App\Models\Ticket;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Contracts\ITicketInterface;
use App\Contracts\IDebtInterface;
use App\Http\Requests\TicketRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Contracts\IDebtRepositoryInterface;

use Illuminate\Console\View\Components\Warn;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Validation\ValidationException;

// use App\Repositories\AdditionRepository;

class DebtService implements IDebtInterface
{
    public int $statusCode;
    public $msg;
    public int $errorCode;
    public string $error;

    public function __construct(private readonly IDebtRepositoryInterface $debtRepository)
    {

    }

    public function updateDebt(int $debId, int $ticketId): bool
    {
        return $this->debtRepository->update($debId, $ticketId);
    }
}
