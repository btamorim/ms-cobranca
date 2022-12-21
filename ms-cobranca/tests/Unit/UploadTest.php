<?php

namespace Tests\Unit;

use App\Services\UploadService;
use Illuminate\Support\Facades\Storage;
use File;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class UploadTest extends TestCase
{
    public function createFileCsvToTest(): string
    {
        $name = date("Y_m_d_H_i_s")."_test.csv";

        $arquivo = fopen(storage_path('app/').$name, 'a+');
        
        $data = [
            ['name','governmentId','email','debtAmount','debtDueDate','debtId'],
            ['John Doe1',11111111111,'johndoe@kanastra.com.br','1000000.00','2022-10-12',8291],
            ['John Doe4',44444444444,'johndoe@kanastra.com.br','700.00','2022-10-12',8294]
        ];

        foreach ($data as $value) {
            fputcsv($arquivo, $value );
        }
        
        fclose($arquivo);

        return $name;
    }

    public function deleteFileToTest($name): void
    {
        Storage::delete($name);
    }

    public function testValidateCanUploadAndSalveInStorage()
    {
        $name = $this->createFileCsvToTest();

        $uploadService = app()->make(UploadService::class);

        $fakeFile = new SymfonyUploadedFile(storage_path('app/').$name, $name, 'csv' );

        $attach = UploadedFile::createFromBase($fakeFile, false);

        $storedFile = $uploadService->storeFile($attach);

        $this->assertTrue($storedFile);

        $this->deleteFileToTest($name);
    }
}
