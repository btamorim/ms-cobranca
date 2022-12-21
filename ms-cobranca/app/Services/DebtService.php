<?php

namespace App\Services;

use App\Contracts\ITicketInterface;
use App\Http\Requests\TicketRequest;
use App\Models\Debt;
use App\Models\Ticket;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Console\View\Components\Warn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

// use App\Repositories\AdditionRepository;

class DebtService
{
    public int $statusCode;
    public $msg;
    public int $errorCode;
    public string $error;

    public function __construct()
    {

    }

    public function updateDebt(int $debId, int $ticketId): bool
    {
        if (is_numeric($debId) && is_numeric($ticketId))
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
        
        return false;
        
    }
    
    


}