<?php

namespace Tests\Unit;

use App\Services\TicketService;
use PHPUnit\Framework\TestCase;
// use GuzzleHttp\Client;
// use GuzzleHttp\Handler\MockHandler;
// use GuzzleHttp\HandlerStack;
// use GuzzleHttp\Psr7\Response;
// // use GuzzleHttp\Psr7\Request;
// use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request as Request;

class TicketTest extends TestCase
{
    public function getTicketIntegrated()
    {
        return [
            "nosso_nro" => "00008291",
            "agencia" => 118,
            "conta" => "95498099",
            "conta_dv" => 8,
            "identificacao" => "Código Aberto de Sistema de Boletos",
            "cedente" => "Razão Social da sua empresa",
            "cpf_cnpj" => "11.111.111/0001-01",
            "sacado" => "John Doe1",
            "identif_Sacado" => 11111111111,
            "valor_cobrado" => 1000000,
            "data_venc" => "2022-10-12",
            "valor_total_boleto" => 1000003.51,
            "bar_code" => "54392099001 74250034798 6548964631668"           
        ];
    }

    public function getCharge()
    {
        return [
            "name" => "John Doe1",
            "governmentId" => 11111111111,
            "email" => "johndoe@kanastra.com.br",
            "debtAmount" => 1000000.0,
            "debtDueDate" => "2022-10-12",
            "debtId" => 8291
        ];
    }
    
    /** @test */
    public function parse_data_ticket_to_format_checkout()
    {

        $ticket = [
            'debtId'   => rand(1,9999),
            'paidAt'   => "2022-06-09 10:00:00",
            'paidBy'   => "John Doe" ,
            'paidAmount' => 1110,10,
            'statusId' => 1,
        ];

        $ticketService = app()->make(TicketService::class);

        $arrayReturn = $ticketService->parseTicketCheckout($ticket);

        $this->assertIsArray($arrayReturn);
    }

    /** @test */
    public function find_ticket_for_debtId()
    {
        $ticketService = app()->make(TicketService::class);

        $tickedId = $ticketService->findTicket(1);

        $this->assertIsInt($tickedId);
    }

    /** @test */
    public function update_ticket_whith_data_on_request()
    {
        $data = [
            "debtId" => "68063719816",
            "paidBy" => "TESTE",
            "paidAmount" => "4111111.0123",
            "paidAt" => "01/09/2023"
        ];

        $ticketService = app()->make(TicketService::class);

        $tickedId = $ticketService->findTicket(1);

        $updated = $ticketService->updateTicket($tickedId, $data);

        $this->assertTrue($updated);
    }

    /** @test */
    //error ainda validar porque esta extendendo outra class
    // public function store_ticket_whith_data_on_csvList_integrate()
    // {
        // $data = $this->getTicketIntegrated();

        // $charge = $this->getCharge();

    //     $ticketService = app()->make(TicketService::class);

    //     $ticket = $ticketService->storeTicket(array_merge($data, $charge));

    //     $this->assertIsInt($ticket->ticketId);
    // }

    /** @test */
    public function parse_data_on_csvList_integrate_to_insert()
    {
        $data = $this->getTicketIntegrated();

        $charge = $this->getCharge();

        $ticketService = app()->make(TicketService::class);

        $ticketParsed = $ticketService->parseTicketInsert(array_merge($data, $charge));

        $this->assertIsArray($ticketParsed);
    }

    // /** @test */
    public function validate_data_on_csvList_to_storage_to_ticket()
    {
        $charge = $this->getCharge();

        $data = $this->getTicketIntegrated();

        $ticketService = app()->make(TicketService::class);

        $validation = $ticketService->validateTicket(array_merge($data, $charge));

        $this->assertTrue($validation);
    }

    /** @test */
    public function check_was_checkoutTicket_is_working()
    {   
        $request = new Request();

        $data = [
            "debtId" => "68063719816",
            "paidBy" => "TESTE",
            "paidAmount" => "4111111101.23",
            "paidAt" => "01/09/2023"
        ];

        $request->query->add($data);

        $ticketService = app()->make(TicketService::class);

        $validation = $ticketService->checkoutTicket($request);

        $this->assertTrue($validation);

    }
    
}
