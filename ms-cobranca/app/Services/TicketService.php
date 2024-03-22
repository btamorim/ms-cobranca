<?php

namespace App\Services;

use Exception;
use Throwable;
use PDOException;
use App\DTO\TicketDTO;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Contracts\ITicketInterface;
use App\Http\Requests\TicketRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Contracts\ITicketRepositoryInterface;
use Illuminate\Validation\ValidationException;

class TicketService implements ITicketInterface
{
    public string $statusCode;
    public $msg;
    public int $errorCode;
    public string $error;

    public function __construct(public readonly ITicketRepositoryInterface $ticketRepository)
    {}

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

        } catch (Throwable $th) {
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
                throw new Exception("The value is not supported", 1);
            }

            $ticket = [
                'debtId'   => $request['debtId'],
                'paidAt'   => $request['paidAt'],
                'paidBy'   => $request['paidBy'],
                'amount'   => number_format($amount, 2),
                'statusId' => 1,
            ];

            return $ticket;

        } catch (Throwable $th) {

            $this->msg = $th->getMessage();
            $this->errorCode  = 422;
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;

            return false;
        }
    }

    public function findTicket(int $debtId): int
    {
        try {
            $ticket = $this->ticketRepository->findByDebtId($debtId);

            return $ticket->id;

        } catch (QueryException $e) {

            throw new Exception("Ticket not found", 404);

        } catch (PDOException $e) {

            throw new PDOException("Error connecting to DB", 409);

        } catch (Throwable $e) {

            throw new Exception($e->getMessage(), $e->getCode());

        }
    }

    public function updateTicket(int $ticketId, array $data): bool
    {

        try {
            $this->ticketRepository->update($ticketId, $data);

            $this->statusCode = StatusServiceEnum::STATUS_CODE_SUCCESSO->value;
            $this->msg = 'Ticket downloaded successfully.';

            return true;

        } catch (Throwable $th) {
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO->value;
            $this->msg = "It was not possible to download the ticket. Error: {$th->getMessage()}";

        }

        return false;
    }

    public function storeTicket(array $data): ?Ticket
    {
        try {

            $ticketDTO = new TicketDTO(
                debtId: $data['debtId'],
                governmentId: $data['cpf_cnpj'],
                amount: $data['debtAmount'],
                debtDueDate: $data['debtDueDate'],
                barCode: $data['bar_code'],
                bankId: 1,
                customerId: rand(1, 900),
            );

            $this->validateTicket($ticketDTO->toArray());

            return $this->ticketRepository->save($ticketDTO);

        } catch (Throwable $th) {
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->msg = $th->getMessage();

            return false;
        }
    }

    public function validateTicket(array $attributes): bool
    {
        $ticketRequest = new TicketRequest();

        $rules = $ticketRequest->createTicketRules();

        $message = $ticketRequest->messages();

        $validator = Validator::make($attributes, $rules, $message);

        if($validator->fails())
            throw new ValidationException($validator);

        return true;

    }
}
