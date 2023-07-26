<?php

namespace App\Services;

use App\Exceptions\ExtractDataJsonException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JsonData
{
    protected array $content;
    const IMAGE_URL = 'https://images.crunchbase.com/c_lpad,h_170,w_170,f_auto,b_white,q_auto:eco,dpr_1/';
    const REQUIRED_KEYS = ['category_id', 'properties.identifier.value'];

    /**
     * @param string $path path to file with json data
     * @throws ExtractDataJsonException
     */
    public function __construct(string $path)
    {
        $this->extractData($path);
    }

    /**
     * Extract data from json file
     * @param string $path path to file
     * @throws ExtractDataJsonException
     */
    private function extractData(string $path)
    {
        $content = json_decode(Storage::get($path), true);
        if(is_null($content))
            throw new ExtractDataJsonException();

        $this->content = $content;
    }

    /**
     * Get array with extracted data
     * @return \Generator
     */
    public function getData(): \Generator
    {
        foreach ($this->content['entities'] as $app) {

            if(!Arr::has($app, self::REQUIRED_KEYS)) {
                Log::warning($app['uuid'] . " | Cannot find 'category_id' or 'name' field");
                continue;
            }

            yield $this->createArrayData($app);
        }
    }

    /**
     * Create array of data
     * @param array $app
     * @return array
     */
    private function createArrayData(array $app)
    {
        return [
            'category_id' => $app['uuid'],
            'name' => Arr::get($app, 'properties.identifier.value'),
            'description' => Arr::get($app, 'properties.short_description'),
            'website' => 'https://stackdeck.com',
            'icon' => $this->createIconField(
                Arr::get($app, 'properties.identifier.image_id')
            ),
            'location' => $this->createLocationField(
                Arr::get($app, 'properties.location_identifiers')
            ),
            'trail_available' => Arr::get($app, 'properties.trial_availabe'),
            'supports_sso' => Arr::get($app, 'properties.support_sso'),
            'sso_remarks' =>  Arr::get($app, 'properties.sso_remarks'),
            'founded_year' => $this->getYear(
                Arr::get($app,'properties.founded_on.value')
            ),
            'urls' => Arr::get($app, 'properties.app_urls', []),
            'crunchbase_id' => Arr::get($app, 'properties.identifier.uuid'),
        ];
    }

    /**
     * Create icon field or return default value
     * @param string|null $imageId
     * @param mixed|null $default
     * @return mixed
     */
    private function createIconField(null|string $imageId, mixed $default = null): mixed
    {
        return is_null($imageId)
                ? $default
                : self::IMAGE_URL . $imageId;
    }

    /**
     * Create location field or return default value
     * @param array|null $data
     * @param mixed $default
     * @return mixed
     */
    private function createLocationField(null|array $data, mixed $default = ''): mixed
    {
        if(is_null($data) || count($data) === 0)
            return $default;

        return Arr::join(
            Arr::pluck($data, 'value'),
            ', '
        );
    }

    /**
     * Extract year from date or return default value
     * @param string|null $date
     * @param mixed|null $default
     * @return mixed
     */
    private function getYear(null|string $date, mixed $default = null): mixed
    {
        return is_null($date)
            ? $default
            : date('Y', strtotime($date));
    }
}
