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

class ImportApp implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected FileUpload $file;

    /**
     * Create a new job instance.
     */
    public function __construct(FileUpload $file)
    {
        $this->file = $file;
    }

    /**
     * The unique ID of the job.
     *
     * @return int
     */
    public function uniqueId()
    {
        return $this->file->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $jsonData = new JsonData($this->file->path);
        $api = new StackDeckApi();

        foreach ($jsonData->getData() as $app) {
            $api->createApp($app);
        }
    }
}
