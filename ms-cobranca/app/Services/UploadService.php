<?php

namespace App\Services;


use App\Contracts\IUploadInterface;
use App\Http\Requests\UploadRequest;
use Illuminate\Http\UploadedFile;
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

    public function storeFile(UploadedFile $request): bool
    {
        try {
            Storage::disk('local')->put('', $request);

            return true;

        } catch (UnableToWriteFile $e) {
            $this->msg = "problem writing the file!";
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->errorCode = $e->getCode();
            $this->error = $e->getMessage();

            return false;

        } catch (\Throwable $th) {
            $this->msg = "problem writing the file!";
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->errorCode = $th->getCode();
            $this->error = $th->getMessage();

            return false;
        }
    }

}