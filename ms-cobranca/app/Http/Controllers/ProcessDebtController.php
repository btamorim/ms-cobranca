<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessListDebt;
use App\Services\StatusServiceEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProcessDebtController extends Controller
{
    public function ProcessJob(Request $request): JsonResponse
    {
        try {

            dispatch(new ProcessListDebt());

            return response()->json([
                'statusCode' => StatusServiceEnum::STATUS_CODE_SUCCESSO,
                'msg' => 'The list of debits has been processed!'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'statusCode' => StatusServiceEnum::STATUS_CODE_ERRO,
                'msg' => $th->getMessage()
            ], 400);
        }
    }
}
