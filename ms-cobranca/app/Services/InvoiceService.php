<?php

namespace App\Services;


use Throwable;
use Ramsey\Uuid\Uuid;
use App\DTO\ChargeDTO;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Jobs\ProcessListDebt;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\UploadedFile;
use App\Contracts\IInvoiceInterface;
use App\Http\Requests\UploadRequest;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use App\Traits\ConsumerExternalServicesTrait;

class InvoiceService implements IInvoiceInterface
{
    public string $statusCode;
    public $msg;
    public int $errorCode;
    public string $error;
    private string $ticketUrl;
    private string $key;
    private string $appKey;

    public function __construct(private readonly Client $client)
    {
        $this->ticketUrl = config('services.ticket.url');
        $this->key = config('services.ticket.key');
        $this->appKey = config('services.ticket.appKey');
    }

    public function generateInvoice(ChargeDTO $chargeDTO): array
    {
        try {
            if (isset($this->key)) {
                $headers['Authorization'] = $this->key;
                $headers['Content-Type']  = 'application/json';
            }

            if (isset($this->appKey)) {
                $headers['AppKey'] = $this->appKey;
            }

            $data = $chargeDTO->toArray();

            /**
             * If it was a real app, it would be ready to submit for integration to generate the invoice
             */
            //$response = $client->request('POST', $this->baseUrl, ['body' => $data, 'headers' => $headers]);

            //create a fakeResponse
            $response = new Response(200, ['X-Header' => 'hoge'], $this->responseFakeData($data));

            return [
                "code" => $response->getStatusCode(),
                "data" => \json_decode($response->getBody(), true),
                "status" => true
            ];

        } catch (Throwable $th) {
            return [
                "code" => $th->getCode(),
                "data" => $th->getMessage(),
                "status" => false
            ];
        }
    }

    public function responseFakeData(array $data): string
    {
        return json_encode([
            'nosso_nro' => sprintf('%08s',$data['debtId']),
            'agencia' => rand(1,999),
            'conta' => sprintf('%08s', rand(1,99999999)),
            'conta_dv' => rand(0,9),
            'identificacao' => 'Código Aberto de Sistema de Boletos',
            'cedente' => 'Razão Social da sua empresa',
            'cpf_cnpj' => '11.111.111/0001-01',
            'sacado' => $data['name'],
            'identif_Sacado'=> $data['governmentId'],
            'valor_cobrado' => $data['debtAmount'],
            'data_venc' => $data['debtDueDate'],
            'valor_total_boleto' => floatval($data['debtAmount'] + 3.5),
            'bar_code' => sprintf('%10s', rand(1,99999999999)).' '. sprintf('%10s', rand(1,99999999099)).' 6548964631668'
        ]);
    }
}
