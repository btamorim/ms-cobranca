<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use Illuminate\Http\Request;
use App\Services\TicketService;


class TicketController extends Controller
{

    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }

    /**
     * receive confirmation of ticket creation
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentConfirmation(TicketRequest $request)
    {
        $ticketService = app()->make(TicketService::class);
        
        $ticketService->checkoutTicket($request);
        
        if ($ticketService->statusCode == 'ERROR') {
            
            return response()->json([
                'statusCode' => $ticketService->statusCode,
                'msg' => $ticketService->msg
            ], !empty($ticketService->errorCode) ? $ticketService->errorCode : 422);
        }

        return response()->json([
            'statusCode' => $ticketService->statusCode,
            'msg' => $ticketService->msg,
        ]);

    }
}
