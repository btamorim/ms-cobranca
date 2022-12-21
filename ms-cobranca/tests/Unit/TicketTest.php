<?php

namespace Tests\Unit;

use App\Services\TicketService;
use PHPUnit\TestCase;
use Illuminate\Http\Request as Request;
use Tests\TestCase as TestsTestCase;

class TicketTest extends TestsTestCase
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
            "governmentId" => "11.111.111/0001-01",
            "email" => "johndoe@kanastra.com.br",
            "debtAmount" => 1000000.0,
            "debtDueDate" => "2022-10-12",
            "debtId" => 8291
        ];
    }
    
    public function testParseDataTicketToFormatCheckout()
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

    public function testFindTicketForDebtId()
    {
        $ticketService = app()->make(TicketService::class);

        $tickedId = $ticketService->findTicket(1);

        $this->assertIsInt($tickedId);
    }

    public function testUpdateTicketWhithDataOnRequest()
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

    public function testStoreTicketWhithDataOnCsvListIntegrate()
    {
        $data = $this->getTicketIntegrated();

        $charge = $this->getCharge();

        $ticketService = app()->make(TicketService::class);

        $ticket = $ticketService->storeTicket(array_merge($data, $charge));

        $this->assertIsInt($ticket->ticketId);
    }

    public function testParseDataOnCsvListIntegrateToInsert()
    {
        $data = $this->getTicketIntegrated();

        $charge = $this->getCharge();

        $ticketService = app()->make(TicketService::class);

        $ticketParsed = $ticketService->parseTicketInsert(array_merge($data, $charge));

        $this->assertIsArray($ticketParsed);
    }

    public function testValidateDataOnCsvListToStorageToTicket()
    {
        $charge = $this->getCharge();

        $data = [
            "debtId" => 8291,
            "customerId" => 248,
            "governmentId" => "11.111.111/0001-01",
            "amount" => 1000000.0,
            "debtDueDate" => "2022-10-12",
            "bankId" => 1,
            "barCode" => "7672404584 83414934236 6548964631668",
            "status" => 0
        ];

        $ticketService = app()->make(TicketService::class);

        $validation = $ticketService->validateTicket(array_merge($data, $charge, ));

        $this->assertTrue($validation);
    }

    public function testCheckWasCheckoutTicketIsWorking()
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
