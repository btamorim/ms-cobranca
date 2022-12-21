<?php

namespace App\Services;

use App\Contracts\ITicketInterface;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\UploadRequest;
use App\Models\Ticket;
use Faker\Core\File;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Console\View\Components\Warn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;




class UploadService
{
    public string $statusCode;
    public $msg;
    public int $errorCode;
    public string $error;

    public function __construct()
    {

    }

    public function storeFile(Request $request): bool
    {
        try {
            Storage::disk('local')->put('', $request->file('listDebt'));

            return true;

        } catch (\League\Flysystem\UnableToWriteFile $e) {
            $this->msg = "problem writing the file!";
            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->errorCode = $e->getCode();
            $this->error = $e->getMessage();

            return false;

        } catch (\Throwable $th) {
            $this->msg = "problem writing the file!";
            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->errorCode = $th->getCode();
            $this->error = $th->getMessage();

            return false;
        }
    }

}