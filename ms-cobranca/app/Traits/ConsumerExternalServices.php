<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;

trait ConsumerExternalServices
{
    public function performRequest($method, $requestUrl, $data = [])
    {
        try {

            $client = app()->make(Client::class);

            $baseUrl = getenv('INTEGRATE_TICKET_URL');

            $key = getenv('KEY_SERVICE');

            $appKey = getenv('APP_KEY');

            if (isset($key)) {
                $headers['Authorization'] = $key;
                $headers['Content-Type']  = 'application/json';
            }

            if (isset($appKey)) {
                $headers['AppKey'] = $appKey;
            }

            /**
             * if it was a real application it would be ready to send
             */
            //$response = $client->request('POST', $baseUrl, ['body' => $data, 'headers' => $headers]);

            //create a fakeResponse
            $response = new Response(200, ['X-Header' => 'hoge'], $this->responseFakeData($data));

            return [
                "code" => $response->getStatusCode(),
                "data" => \json_decode($response->getBody(), true),
                "status" => true
            ];

        } catch (BadResponseException $th) {
            return [
                "code" => $th->getResponse()->getStatusCode(),
                "data" => \json_decode($th->getResponse()->getBody(), true),
                "status" => false
            ];

        } catch (\Throwable $th) {
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
