<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Jobs\ProcessListDebt;
use App\Services\StatusService;
use App\Services\UploadService;

class UploadController extends Controller
{
    public function UploadCharges(UploadRequest $request)
    {
        try {

            if (!$request->hasFile('listDebt')) {

                return response()->json([
                    'statusCode' => StatusService::STATUS_CODE_ERRO,
                    'msg' => 'The list of debts has not process!'
                ], 400);
            }

            $uploadService = app()->make(UploadService::class);

            if(!$uploadService->storeFile($request->file('listDebt'))) 
            {
                return response()->json([
                    'statusCode' => $uploadService->statusCode,
                    'msg' => $uploadService->msg,
                    'error' => $uploadService->error,
                ], 400);
            }

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
