<?php

namespace App\Services;

use App\Contracts\ITicketInterface;
use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

// use App\Repositories\AdditionRepository;

class TicketService implements ITicketInterface
{
    public string $statusCode;
    public $msg;
    public int $errorCode;
    public string $error;

    public function __construct()
    {

    }

    public function checkoutDebit(): bool
    {
        return true;
    }

    public function checkoutTicket(Request $request): bool
    {
        try {
        
            $data = $request->all();

            $dataPayment = $this->parseTicketCheckout($data);

            $ticketId = $this->findTicket($data['debtId']);

            if (empty($dataPayment))
            {   
                return false;
            }

            $checkoutTick = $this->updateTicket($ticketId, $dataPayment);

            if (!$checkoutTick)
            {
                return false;
            }

            return true;

        } catch (\Throwable $th) {
            $this->msg        = $th->getMessage();
            $this->errorCode  = $th->getCode();
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;

            return false;
        }

    }

    public function parseTicketCheckout(Array $request): ?array
    {
        try{

            if (!$amount = floatval($request['paidAmount'])) 
            {
                throw new \Exception("The value is not supported", 1);
            }

            $ticket = [
                'debtId'   => $request['debtId'],
                'paidAt'   => $request['paidAt'],
                'paidBy'   => $request['paidBy'],
                'amount'   => number_format($amount, 2),
                'statusId' => 1,
            ];
                
            return $ticket;

        } catch (\Throwable $th) {
            
            $this->msg = $th->getMessage();
            $this->errorCode  = 422;
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            
            return false;            
        }
    }

    public function findTicket(int $debtId): int
    {
        try {
        
            /**
             * find in BD this ticket for debtId, but create a fake to run project
             */
            // $ticket = Ticket::where('debitId', $debtId)->firstOrFail();
            
            // return $ticket->ticketId;

            return rand(1, 99999);
        
        } catch (QueryException $e) {

            throw new \Exception("Ticket not found", 404);

        } catch (\PDOException $e) {

            throw new \PDOException("Error connecting to DB", 409);
   
        } catch (\Throwable $e) {

            throw new \Exception($e->getMessage(), $e->getCode());

        }
    }

    public function updateTicket(int $ticketId, array $data): bool
    {
        if (!empty($ticketId))
        {
            try {

                /**
                 * UPDATE in BD this ticket, but create a fake to run project
                 */
                // $ticket = Ticket::where('ticketId', $ticketId)->update($data);
                
                $this->statusCode = StatusServiceEnum::STATUS_CODE_SUCCESSO->value;
                $this->msg = 'Ticket downloaded successfully.';

                return true;
            
            } catch (\Throwable $th) {
                $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO->value;
                $this->msg = "It was not possible to download the ticket. Error: {$th->getMessage()}";

            }
        }
        else
        {
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->msg = 'Ticket not found.';

        }

        return false;
                    
    }

    public function storeTicket(array $data): Ticket
    {
        try {

            $dataTicket = $this->parseTicketInsert($data);

            $this->validateTicket($dataTicket);

            $ticket = new Ticket();

            $ticket->fill($dataTicket);

            /**
             * this create a ticket obejct, but fake() is comment
             */
            // $ticket->save();

            //simulation id BD
            $ticket->ticketId = rand(1,99999);

            return $ticket;

        } catch (\Throwable $th) {
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->msg = $th->getMessage();

            return false;
        }

    }

    public function parseTicketInsert(array $data)
    {
        return [
            "debtId" => $data['debtId'],
            "customerId" => rand(1, 900),
            "governmentId" => $data['cpf_cnpj'],
            "amount" => $data['debtAmount'],
            "debtDueDate" => $data['debtDueDate'],
            "bankId" => 1,
            "barCode" => $data['bar_code'],
            "status" => 0,
        ];
    }

    public function validateTicket(array $attributes): bool
    {
        $ticketRequest = new TicketRequest();

        $rules = $ticketRequest->createTicketRules();

        $message = $ticketRequest->messages();

        $validator = Validator::make($attributes, $rules, $message);

        if ($validator->fails())
            throw new ValidationException($validator);

        return true;

    }
}