<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;

class JsonData
{
    protected array $content;

    public function __construct(string $path)
    {
        $this->content = json_decode(Storage::get($path));
    }


}
