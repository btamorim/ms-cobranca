<?php

namespace App\Services\Debt;

use Exception;
use Throwable;
use App\Traits\Log;
use App\DTO\ChargeDTO;
use Illuminate\Http\Request;
use App\Imports\ChargesImport;
use App\Services\InvoiceService;
use App\Contracts\IDebtInterface;
use App\Contracts\ITicketInterface;
use App\Contracts\IInvoiceInterface;
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

    public function __construct(
        private readonly ITicketInterface $ticketService,
        private readonly IDebtInterface $debtService,
        private readonly IInvoiceInterface $invoiceService,
    )
    {
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

    private function getInvoicesByCharge(ChargeDTO $chargeDTO): array
    {
        $response = $this->invoiceService->generateInvoice($chargeDTO);

        if(!isset($response['status']))
        {
            $this->storeLogData($response, "error_request_integrate_invoice");
            throw new Exception('Error to get Invoice.');
        }

        return $response;
    }

    public function processCharge(ChargeDTO $chargeDTO): bool
    {
        try {
            $invoice = $this->getInvoicesByCharge($chargeDTO);

            $ticket = $this->ticketService->storeTicket(array_merge($invoice['data'], $chargeDTO->toArray()));

            $this->debtService->updateDebt($ticket->ticketId, $chargeDTO->debtId);

            $this->ticketService->publishTicketMail($chargeDTO);

            return true;

        } catch (Throwable $th) { dd($th->getMessage());
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
}
