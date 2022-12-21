<?php

namespace Tests\Unit;

use App\Services\UploadService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class UploadTest extends TestCase
{
    public function create_file_csv_to_test(): string
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

    public function delete_file_to_test($name): void
    {
        Storage::delete($name);
    }


    /** @test */
    public function validate_can_upload_and_salve_in_storage()
    {
        $name = $this->create_file_csv_to_test();

        $uploadService = app()->make(UploadService::class);

        $files = array_map('pathinfo', \File::files(storage_path('app')));

        foreach ($files as $file) {
            if ($file['extension'] === 'csv' && $file['basename'] == $name)
            {
                $fakeFile = new SymfonyUploadedFile(storage_path('app/').$name, $file['basename'], 'csv' );

                $anexo = UploadedFile::createFromBase($fakeFile, false);

                $store = $uploadService->storeFile($anexo);

                $this->assertTrue($store);
            }
        }
    }

}
