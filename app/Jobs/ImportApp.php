<?php

namespace App\Jobs;

use App\Models\FileUpload;
use App\Services\JsonData;
use App\Services\StackDeckApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportApp implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $fileId;
    protected JsonData $jsonData;

    /**
     * Create a new job instance.
     */
    public function __construct(int $fileId, JsonData $jsonData)
    {
        $this->fileId = $fileId;
        $this->jsonData = $jsonData;
    }

    /**
     * The unique ID of the job.
     *
     * @return int
     */
    public function uniqueId()
    {
        return $this->fileId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $api = new StackDeckApi();

        foreach ($this->jsonData->getData() as $app) {
            $api->createApp($app);
        }
    }
}
