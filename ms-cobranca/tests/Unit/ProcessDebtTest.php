<?php

namespace Tests\Unit;

use App\Services\ProcessDebtService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Http\Request as Request;

class ProcessDebtTest extends TestCase
{
    public function testImportCsvByNameAndParseToArray()
    {
        $processDebtService = app()->make(ProcessDebtService::class);

        $name = $this->createFileCsv();

        $arrayReturn = $processDebtService->importCsv(str_replace('.csv', "", $name));

        Storage::delete($name);

        $this->assertIsArray($arrayReturn);
    }

    public function createFileCsv()
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

    public function testValidIsHeadCsvIsEspecified()
    {
        $processDebtService = app()->make(ProcessDebtService::class);

        $title = ['name','governmentId','email','debtAmount','debtDueDate','debtId'];
        
        $validation = $processDebtService->validateTitles($title);

        $this->assertTrue($validation);
    }

    public function getCharge()
    {
        return [
            "name" => "John Doe1",
            "governmentId" => 11111111111,
            "email" => "johndoe@kanastra.com.br",
            "debtAmount" => 1000000.0,
            "debtDueDate" => "2022-10-12",
            "debtId" => 8291
        ];
    }

    public function testValidToPublishTicketInEmail()
    {
        $processDebtService = app()->make(ProcessDebtService::class);

        $publish = $processDebtService->publishTicketMail($this->getCharge());

        $this->assertTrue($publish);
    }

    public function testGetTitleForRequestFileCsv()
    {
        $request = new Request();

        $processDebtService = app()->make(ProcessDebtService::class);

        $processTitle = $processDebtService->getTitle($request);

        $this->assertTrue(empty($processTitle));

    }

    public function testValidProcessListOfDebts()
    {
        $processDebtService = app()->make(ProcessDebtService::class);

        $name = $this->createFileCsv();

        $process = $processDebtService->processListDebtJob(str_replace('.csv', "", $name));

        Storage::delete($name);

        $this->assertTrue($process);
    }
}
