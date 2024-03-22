<?php

namespace App\Services;

use Exception;
use Throwable;
use App\Traits\Log;
use App\DTO\ChargeDTO;
use Illuminate\Http\Request;
use App\Services\DebtService;
use App\Imports\ChargesImport;
use App\Services\TicketService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use App\Contracts\IProcessDebtInterface;
use App\Jobs\notificationChargeCustomer;
use Maatwebsite\Excel\Concerns\Importable;
use App\Traits\ConsumerExternalServicesTrait;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\ValidationException;

class ProcessDebtService implements IProcessDebtInterface
{
    use Importable, ConsumerExternalServicesTrait, Log;

    public $statusCode;
    public $msg;
    public $errorCode;
    private TicketService $ticketService;
    private DebtService $debtService;

    public function __construct(
        TicketService $ticketService,
        DebtService $debtService
    )
    {
        $this->ticketService = $ticketService;
        $this->debtService = $debtService;

       /** Maintain the formatting of the CSV title. */
        HeadingRowFormatter::default('none');
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function processListDebtJob(array $files): void
    {
        foreach ($files as $file) {
            if(!$this->validateExtension($file['extension'])){
                return;
            }

            if($this->processCsv($file['filename'])) {
                Storage::disk('local')->delete($file['basename']);
            }
        }
    }

    public function validateExtension(string $extension): bool
    {
        if ($extension === 'csv')
        {
            return true;
        }

        return false;
    }

    public function processCharge(ChargeDTO $chargeDTO)
    {
        try {
                $response = $this->performRequest('POST', 'transactions', $chargeDTO->toArray());

                if(!isset($response['status']))
                {
                    $this->storeLogData($response, "error_request_integrate_ticket");
                }

                $ticket = $this->ticketService->storeTicket(array_merge($response['data'], $chargeDTO->toArray()));

                if(! $this->debtService->updateDebt($ticket->ticketId, $chargeDTO->debtId))
                {
                    throw new Exception("Error to Update debt", 400);

                }

                if(!$this->publishTicketMail($chargeDTO))
                {
                    return false;
                }

                return true;

        } catch (Throwable $th) {
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->msg = $th->getMessage();
            $this->errorCode = $th->getCode();

            return false;
        }
    }

    private function sanitizeData(array $line): array {
        $line = array_map(function($value) {
            return preg_replace('/["\']/', '', $value);
        }, $line);
        unset($value);

        return $line;
    }

    public function processCsv(string $fileName): bool
    {
        try {
            $file = fopen(Storage::path('').'/'.$fileName.'.csv', 'r');

            $firstLine = null;
            $fist = true;
            while (($line = fgetcsv($file, 0, ',', "'")) !== false)
            {
                if($fist){
                    $fist = false;
                    $firstLine = $this->sanitizeData($line);
                    continue;
                }

                $data = $this->sanitizeData($line);

                $chargeDTO = new ChargeDTO(
                    name: $data[0],
                    governmentId: $data[1],
                    email: $data[2],
                    debtAmount: $data[3],
                    debtDueDate: $data[4],
                    debtId: $data[5]
                );

                $this->processCharge($chargeDTO);
            }

            return true;

        } catch (ValidationException $e) {
            $failures = $e->failures();

            $errors = [];

            foreach ($failures as $failure) {
                $failure->row();
                $failure->attribute();
                array_push($errors, $failure->errors());
                $failure->values();

            }

            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->msg = json_encode($errors);
            $this->errorCode = 400;

            return false;

        } catch (Throwable $th) {
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->msg = $th->getMessage();
            $this->errorCode = 400;

            return false;

        } finally {
            fclose($file);
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

            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->msg = json_encode($errors);
            $this->errorCode = 400;

            return false;

        } catch (Throwable $th) {
            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
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
            throw new Exception("Uploaded file is empty!", 400);
        }
        else if(empty($diff)){

            return true;
        }
        else
        {
            throw new Exception("The data sent does not match what was expected!", 400);
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

        } catch (Throwable $th) {

            $this->statusCode = StatusServiceEnum::STATUS_CODE_ERRO;
            $this->msg = "Invalid Spreadsheet file";
            $this->errorCode = 400;

            return [];
        }

    }

    public function publishTicketMail(ChargeDTO $attributesDTO): bool
    {
        try {
            notificationChargeCustomer::dispatch($attributesDTO->toArray());

        } catch (Throwable $th) {
            $this->storeLogData(['message' => $th->getMessage()], "error_request_integrate_ticket");

            return false;
        }

        return true;

    }
}
