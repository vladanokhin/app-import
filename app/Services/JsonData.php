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
    const REQUIRED_KEYS = ['properties.identifier.value'];

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

            if(!Arr::has($app, self::REQUIRED_KEYS)
                    || $this->isEmptyValues($app, self::REQUIRED_KEYS))
            {
                Log::warning($app['uuid'] . " | Cannot find one of required keys field: " . Arr::join(self::REQUIRED_KEYS, ', '));
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
    private function createArrayData(array $app): array
    {
        $data = [
            'category_id' => '41834f5c-5d72-4757-8d80-2741da19bac8',
            'name' => Arr::get($app, 'properties.identifier.value'),
            'description' => Arr::get($app, 'properties.short_description'),
            'website' => Arr::get($app, 'properties.website.value'),
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
                Arr::get($app,'properties.founded_on.value'),

            ),
            'urls' => Arr::get($app, 'properties.app_urls'),
            'crunchbase_id' => Arr::get($app, 'properties.identifier.uuid'),
        ];

        return Arr::where($data, function ($value, $key) {
            return  is_string($value) && strlen($value) > 0
                    || is_array($value)
                    && !$this->isEmptyValues($value, array_keys($value))
                    && count($value) > 0
                    || is_bool($value);
        });
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
    private function createLocationField(null|array $data, mixed $default = null): mixed
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

    /**
     * Check if the received value is a string and not empty
     * @param array $array
     * @param array $keys
     * @return bool
     */
    private function isEmptyValues(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            $value = Arr::get($array, $key, '');
            if(is_string($value) && strlen($value) === 0)
                return true;
        }

        return false;
    }
}
