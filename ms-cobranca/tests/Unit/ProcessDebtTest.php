<?php

namespace Tests\Unit;

use App\Services\ProcessDebtService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Http\Request as Request;

class ProcessDebtTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function import_csv_by_name_and_parse_to_array()
    {
        $processDebtService = app()->make(ProcessDebtService::class);

        $name = $this->create_file_csv();

        $arrayReturn = $processDebtService->importCsv(str_replace('.csv', "", $name));

        Storage::delete($name);

        $this->assertIsArray($arrayReturn);
    }

    public function create_file_csv()
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

    /** @test */
    public function valid_is_head_csv_is_especified()
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

    /** @test */
    public function valid_to_publish_ticket_in_email()
    {
        $processDebtService = app()->make(ProcessDebtService::class);

        $publish = $processDebtService->publishTicketMail($this->getCharge());

        $this->assertTrue($publish);
    }

     /** @test */
    public function test_get_title_for_request_file_csv()
    {
        $request = new Request();

        $processDebtService = app()->make(ProcessDebtService::class);

        $processTitle = $processDebtService->getTitle($request);

        $this->assertTrue(empty($processTitle));

    }

     /** @test */
    public function valid_process_list_of_debts()
    {
        $processDebtService = app()->make(ProcessDebtService::class);

        $name = $this->create_file_csv();

        $process = $processDebtService->processListDebtJob(str_replace('.csv', "", $name));

        Storage::delete($name);

        $this->assertTrue($process);
    }
}
