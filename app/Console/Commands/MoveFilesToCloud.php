<?php

namespace App\Console\Commands;

use App\Services\FileService;
use Illuminate\Console\Command;

class MoveFilesToCloud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:move';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move files to cloud';

    /**
     * Execute the console command.
     *
     * @param FileService $fileService
     * @return mixed
     */
    public function handle(FileService $fileService)
    {
        $fileService->moveToCloud();
    }
}
