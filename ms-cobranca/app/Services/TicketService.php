<?php

namespace App\Services;

use Exception;
use Throwable;
use PDOException;
use App\Traits\Log;
use App\DTO\ChargeDTO;
use App\DTO\TicketDTO;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\DTO\TicketCheckoutDTO;
use App\Contracts\ITicketInterface;
use App\Services\StatusServiceEnum;
use App\Http\Requests\TicketRequest;
use Illuminate\Database\QueryException;
use App\Jobs\notificationChargeCustomer;
use Illuminate\Support\Facades\Validator;
use App\Contracts\ITicketRepositoryInterface;
use Illuminate\Validation\ValidationException;

class TicketService implements ITicketInterface
{
    use Log;

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

    public function checkoutTicket(TicketCheckoutDTO $ticketCheckoutDTO): bool
    {
        try {
            $ticketId = $this->findTicket($ticketCheckoutDTO->debtId);

            $checkoutTick = $this->updateTicket($ticketId, $ticketCheckoutDTO->toArray());

            return true;

        } catch (Throwable $th) {
            $this->msg        = $th->getMessage();
            $this->errorCode  = $th->getCode();
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;

            return false;
        }

    }

    public function findTicket(int $debtId): int
    {
        try {
            $ticket = $this->ticketRepository->findByDebtId($debtId);

            return $ticket->ticketId;

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

    public function publishTicketMail(ChargeDTO $attributesDTO):void
    {
        try {
            notificationChargeCustomer::dispatch($attributesDTO->toArray());

        } catch (Throwable $th) {
            $this->storeLogData(['message' => $th->getMessage()], "error_dispaching_ticket_mail");

            throw $th;
        }
    }
}
