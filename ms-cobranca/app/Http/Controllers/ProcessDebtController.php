<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessListDebt;
use App\Services\StatusService;
use Illuminate\Http\Request;

class ProcessDebtController extends Controller
{
    public function ProcessJob(Request $request)
    {
        try {

            dispatch(new ProcessListDebt())->delay(3);

            return response()->json([
                'statusCode' => StatusService::STATUS_CODE_SUCCESSO,
                'msg' => 'The list of debits has been processed!'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => StatusService::STATUS_CODE_ERRO,
                'msg' => $th->getMessage()
            ], 400);
        }
    }
}
