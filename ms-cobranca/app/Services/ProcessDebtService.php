<?php

namespace App\Services;

use App\Contracts\IProcessDebtInterface;
use App\Imports\ChargesImport;
use App\Jobs\notificationChargeCostumer;
use App\Traits\ConsumerExternalServices;
use App\Traits\Log;
use Illuminate\Http\Request;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\Importable;

class ProcessDebtService implements IProcessDebtInterface
{
    use Importable, ConsumerExternalServices, Log;
    
    public $statusCode;
    public $msg;
    public $errorCode;

    public function __construct()
    {
        //não mudar formatação do titulo do csv
        HeadingRowFormatter::default('none');
    }
    
    /**
     * caso procise alterar a linha de titulo
     */
    public function headingRow(): int
    {
        return 1;
    }

    public function processListDebtJob(string $fileName): bool
    {
        try {
            $charges = $this->importCsv($fileName);

            foreach ($charges[0] as $charge) {

                $response = $this->performRequest('POST', 'transactions', $charge);

                if(!isset($response['status']))
                {
                    $this->storeLogData($response, "error_request_integrate_ticket");
                    
                    return false;
                }

                $ticketService = app()->make(TicketService::class);

                $ticket = $ticketService->storeTicket(array_merge($response['data'], $charge));

                $debtService = app()->make(DebtService::class);

                $debtService->updateDebt($ticket->ticketId, $charge['debtId']);

                if(!$this->publishTicketMail($charge))
                {
                    return false;
                }

                return true;
            }

            return true;
            
        } catch (\Throwable $th) {
            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->msg = $th->getMessage();
            $this->errorCode = $th->getCode();

            return false;
        }
    }

    public function importCsv(string $fileName): ?array
    {
        try {

            $charges = Excel::toArray(new ChargesImport, $fileName.'.csv');

            return $charges;

        } catch (ValidationException $e) {
            $failures = $e->failures();
            
            $errors = [];
            
            foreach ($failures as $failure) {
                $failure->row();
                $failure->attribute();
                array_push($errors, $failure->errors());
                $failure->values();

            }

            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->msg = json_encode($errors);
            $this->errorCode = 400;

            return false;

        } catch (\Throwable $th) {
            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->msg = $th->getMessage();
            $this->errorCode = 400;

            return false;
        }
    }

    public function processListDebt(Request $request): bool
    {
        try {

            $title = $this->getTitle($request);

            $this->validateTitles($title);

            $charges = Excel::toArray(new ChargesImport, $request->file('listDebt'));

            return $charges;

        } catch (ValidationException $e) {
            $failures = $e->failures();
            
            $errors = [];
            
            foreach ($failures as $failure) {
                $failure->row();
                $failure->attribute();
                array_push($errors, $failure->errors());
                $failure->values();

            }

            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->msg = json_encode($errors);
            $this->errorCode = 400;

            return false;

        } catch (\Throwable $th) {
            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->msg = $th->getMessage();
            $this->errorCode = 400;

            return false;
        }
    }

    public function validateTitles(array $title): bool
    {
        $defaultTitile = [
            'name',
            'governmentId',
            'email',
            'debtAmount',
            'debtDueDate',
            'debtId'
        ];

        $diff = array_diff($defaultTitile, $title);

        if (empty($title))
        {
            throw new \Exception("O arquivo enviado esta vazio!", 400);
        }
        else if(empty($diff)){

            return true;
        }
        else
        {
            throw new \Exception("Os dados enviados não correspondem ao esperado!", 400);
        }

        return false;
    }

    /**
     * pega os dados do header do arquivo
     */
    public function getTitle(Request $request): array
    {
        try {

            $title = $headings = (new HeadingRowImport)->toArray($request->file('listDebt'));

            return $title[0][0];

        } catch (\Throwable $th) {

            $this->statusCode = StatusService::STATUS_CODE_ERRO;
            $this->msg = "Invalid Spreadsheet file";
            $this->errorCode = 400;

            return [];
        }
        
    }

    public function publishTicketMail(array $attributes): bool
    {
        try {
            notificationChargeCostumer::dispatch($attributes);
            
        } catch (\Throwable $th) {
            $this->storeLogData(['message' => $th->getMessage()], "error_request_integrate_ticket");
            
            return false;
        }

        return true;

    }
}