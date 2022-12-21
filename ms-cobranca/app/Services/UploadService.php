<?php

namespace App\Services;


use App\Contracts\IUploadInterface;
use App\Http\Requests\UploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;

class UploadService implements IUploadInterface
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

        } catch (UnableToWriteFile $e) {
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