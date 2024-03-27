<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\ITicketInterface;
use App\Http\Requests\TicketRequest;


class TicketController extends Controller
{

    public function __construct(private readonly ITicketInterface $ticketService)
    {}

    /**
     * receive confirmation of ticket creation
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function paymentConfirmation(TicketRequest $request)
    {
        $data = $request->parseToDTO();

        $this->ticketService->checkoutTicket($data);

        if ($this->ticketService->statusCode == 'ERROR') {

            return response()->json([
                'statusCode' => $this->ticketService->statusCode,
                'msg' => $this->ticketService->msg
            ], !empty($this->ticketService->errorCode) ? $this->ticketService->errorCode : 422);
        }

        return response()->json([
            'statusCode' => $this->ticketService->statusCode,
            'msg' => $this->ticketService->msg,
        ]);

    }
}
