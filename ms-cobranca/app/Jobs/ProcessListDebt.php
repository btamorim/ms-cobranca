<?php

namespace App\Jobs;

use File;
use Illuminate\Bus\Queueable;
use App\Services\ProcessDebtService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessListDebt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @return void
     */
    public function handle(ProcessDebtService $processDebtService)
    {
        $files = array_map('pathinfo', File::files(storage_path('app')));

        if ($files) {
            $processDebtService->processListDebtJob($files);
        }
    }
}
