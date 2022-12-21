<?php

namespace App\Jobs;

use App\Services\ProcessDebtService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessListDebt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $files = array_map('pathinfo', \File::files(storage_path('app')));

        foreach ($files as $file) {
            if ($file['extension'] === 'csv')
            {
                $listDebt = $file['filename'];

                $processDebtService = app()->make(ProcessDebtService::class);

                if (!$process = $processDebtService->processListDebtJob($listDebt))
                {
                    continue;
                }

                Storage::delete($listDebt.".".$file['extension']);
            }
        }
    }
}
