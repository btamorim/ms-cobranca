<?php

namespace App\Services;

enum StatusServiceEnum : string
{
    case STATUS_CODE_ERRO = 'ERROR';
    case STATUS_CODE_CAMPOS_INVALIDOS  = 'INVALID_FIELDS';
    case STATUS_CODE_SUCCESSO  = 'SUCCESS';
    case STATUS_CONFIRMADO = 'CONFIRMED';
    case STATUS_CODE_DUPLICADO = 'DUPLICITY';
}