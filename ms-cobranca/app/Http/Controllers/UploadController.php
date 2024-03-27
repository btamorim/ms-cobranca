<?php

namespace App\Http\Controllers;

use Throwable;
use App\Jobs\ProcessListDebt;
use App\Services\StatusService;
use Illuminate\Http\JsonResponse;
use App\Contracts\IUploadInterface;
use App\Services\StatusServiceEnum;
use App\Http\Requests\UploadRequest;


class UploadController extends Controller
{

    public function __construct(private readonly IUploadInterface $uploadService)
    {}

    public function UploadCharges(UploadRequest $request): JsonResponse
    {
        try {
            if(!$this->uploadService->storeFile($request->file('listDebt')))
            {
                throw new Exception($this->uploadService->msg);
            }

            return response()->json([
                'statusCode' => StatusServiceEnum::STATUS_CODE_SUCCESSO,
                'msg' => 'The list of debits has been processed!'
            ], 200);

        } catch (Throwable $th) {
            return response()->json([
                'statusCode' => StatusServiceEnum::STATUS_CODE_ERRO,
                'msg' => $th->getMessage()
            ], 400);
        }
    }
}
