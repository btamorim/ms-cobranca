<?php

namespace App\Services;


use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Contracts\IUploadInterface;
use App\Http\Requests\UploadRequest;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use App\Jobs\ProcessListDebt;
use Ramsey\Uuid\Uuid;

class UploadService implements IUploadInterface
{
    public string $statusCode;
    public $msg;
    public int $errorCode;
    public string $error;

    public function storeFile(UploadedFile $request): bool
    {
        try {
            $path = Storage::disk('local')->putFileAs('', $request, Uuid::uuid4()->toString().'.csv');
            chmod(storage_path('app/' . $path), 0777);

            dispatch(new ProcessListDebt());
            return true;

        } catch (Throwable $th) {
            $this->msg = "problem writing the file!";
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->errorCode = $th->getCode();
            $this->error = $th->getMessage();

            return false;
        }
    }
}
