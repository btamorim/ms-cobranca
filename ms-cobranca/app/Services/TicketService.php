<?php

namespace App\Services;

use App\Contracts\ITicketInterface;
use App\Http\Requests\TicketRequest;
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
            $this->statusCode = StatusService::STATUS_CODE_ERRO;

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
                'amount'   => $amount,
                'statusId' => 1,
            ];
                
            return $ticket;

        } catch (\Throwable $th) {
            
            $this->msg = $th->getMessage();
            $this->errorCode  = 422;
            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            
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
                
                $this->statusCode = StatusService::STATUS_CODE_SUCCESSO;
                $this->msg = 'Ticket downloaded successfully.';

                return true;
            
            } catch (\Throwable $th) {
                $this->statusCode = StatusService::STATUS_CODE_ERRO;
                $this->msg = "It was not possible to download the ticket. Error: {$th->getMessage()}";

            }
        }
        else
        {
            $this->statusCode = StatusService::STATUS_CODE_ERRO;
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

            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->msg = $th->getMessage();

            return false;
        }

    }

    public function parseTicketInsert(array $data)
    {
        return [
            "debtId" => $data['debtId'],
            "costumerId" => rand(1, 900),
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

    // public function integrateTicket(array $data): bool
    // {
    //     try {
           
    //         $client = app()->make(Client::class);

    //         $baseUrl = getenv('INTEGRATE_TICKET_URL');

    //         if (isset($this->key)) {
    //             $headers['Authorization'] = $this->key;
    //             $headers['Content-Type']  = 'application/json';
    //         }

    //         if (isset($this->jwt)) {
    //             $headers['Authorization'] = $this->jwt;
    //         }

    //         if (isset($this->appKey)) {
    //             $headers['AppKey'] = $this->appKey;
    //         }
            
    //         /**
    //          * caso fosse uma url real, aqui de fato faz o acesso a API externa
    //          */

    //         /*
    //             $response = $client->request('POST', $baseUrl, ['body' => $data, 'headers' => $headers, 'verify' => false]);
            
    //             $this->statusCode = $response->getStatusCode();
    //             $this->msg = \json_decode($response->getBody(), true);

    //             return true; 
    //         */

    //         $responses = $data[0];

    //         foreach ($responses as $data) {

    //             $tickets['data'] = [
    //                 'nosso_nro' => sprintf('%08s',$data['debtId']),
    //                 'agencia' => rand(1,999),
    //                 'conta' => sprintf('%08s', rand(1,99999999)),
    //                 'conta_dv' => rand(0,9),
    //                 'identificacao' => 'Código Aberto de Sistema de Boletos',
    //                 'cedente' => 'Razão Social da sua empresa',
    //                 'cpf_cnpj' => '11.111.111/0001-01',
    //                 'sacado' => $data['name'],
    //                 'identif_Sacado'=> $data['governmentId'],
    //                 'valor_cobrado' => $data['debtAmount'],
    //                 'data_venc' => $data['debtDueDate'],
    //                 'valor_total_boleto' => ($data['debtAmount'] + 3.5)
    //             ];

    //         }

    //         $this->statusCode = 200;
    //         $this->msg = $tickets;

    //         return true;

    //     } catch (BadResponseException $th) {
    //         $this->statusCode = StatusService::STATUS_CODE_ERRO;
    //         $this->msg = \json_decode($th->getResponse()->getBody(), true);
    //         $this->error = $th->getMessage();
    //         $this->errorCode = $th->getResponse()->getStatusCode() ?? $th->getCode();

    //         return false;

    //     } catch (\Throwable $th) {
    //         $this->statusCode = StatusService::STATUS_CODE_ERRO;
    //         $this->msg = $th->getMessage();
    //         $this->error = $th->getMessage();
    //         $this->errorCode = 500;

    //         return false;
    //     }
    // }

    // public function storeInStorage(array $dados): void
    // {
    //     $boletos = fopen(now().'_boletos.txt', 'a+');
        
    //     fwrite($boletos,json_encode($dados['data']) );

    //     fclose($boletos);
    // }


}