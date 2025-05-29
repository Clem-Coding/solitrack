<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GeocoderService
{
    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache
    ) {}

    /**
     * Retrieves the latitude and longitude coordinates for a given city and postcode.
     *
     * Combines the city and postcode into a single query, then uses a cached HTTP request
     * to fetch geolocation data from the French government API.
     *
     * @param string $city The name of the city.
     * @param string $postcode The postal code.
     * @return array|null Returns an associative array with keys 'lat', 'lon', 'city', and 'postcode' 
     *                    if found, or null if no data is available.
     */
    public function geocode(string $city, string $postcode): ?array
    {
        $query = $postcode . ' ' . $city;

        return $this->cache->get('geocode_' . md5($query), function (ItemInterface $item) use ($query) {
            $item->expiresAfter(86400);

            $url = 'https://api-adresse.data.gouv.fr/search/';

            $response = $this->client->request('GET', $url, [
                'query' => [
                    'q' => $query,
                    'limit' => 1,
                ]
            ]);

            $data = $response->toArray();

            if (!empty($data['features'])) {
                $coords = $data['features'][0]['geometry']['coordinates'];
                $city = $data['features'][0]['properties']['city'] ?? 'Ville inconnue';
                $postcode = $data['features'][0]['properties']['postcode'] ?? '';
                return [
                    'lat' => $coords[1],
                    'lon' => $coords[0],
                    'city' => $city,
                    'postcode' => $postcode,
                ];
            }

            return null;
        });
    }
}
