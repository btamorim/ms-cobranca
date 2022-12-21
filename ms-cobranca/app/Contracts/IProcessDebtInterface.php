<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface IProcessDebtInterface
{
    public function processListDebtJob(string $fileName): bool;  
}