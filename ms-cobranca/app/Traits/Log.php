<?php

namespace App\Traits;

trait Log
{
    /**
     * Store response on new relic
     * 
     * @param array $data
     * @param string $eventName
     * @return void 
     */
    private function storeLogData(array $data, string $eventName): void
    {
        $arquivo = fopen(now()."_{$eventName}.txt", 'a+');
        
        fwrite($arquivo,json_encode($data) );

        fclose($arquivo);
    }
}
