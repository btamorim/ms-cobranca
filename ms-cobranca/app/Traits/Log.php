<?php

namespace App\Traits;

trait Log
{
    /**
     * Store response on local or new relic
     *
     * @param array $data
     * @param string $eventName
     */
    private function storeLogData(array $data, string $eventName): void
    {
        $arquivo = fopen(now()."_{$eventName}.txt", 'a+');

        fwrite($arquivo,json_encode($data) );

        fclose($arquivo);
    }
}
