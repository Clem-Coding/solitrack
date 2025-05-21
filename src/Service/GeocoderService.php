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

    public function geocodeByPostalCode(string $zipcode): ?array
    {
        return $this->cache->get('geocode_' . $zipcode, function (ItemInterface $item) use ($zipcode) {
            $item->expiresAfter(86400);

            $url = 'https://api-adresse.data.gouv.fr/search/';

            $response = $this->client->request('GET', $url, [
                'query' => [
                    'q' => $zipcode,
                    'limit' => 1,
                ]
            ]);

            $data = $response->toArray();

            if (!empty($data['features'])) {
                $coordinates = $data['features'][0]['geometry']['coordinates'];
                $city = $data['features'][0]['properties']['city'] ?? 'Ville inconnue';
                return [
                    'lat' => $coordinates[1],
                    'lon' => $coordinates[0],
                    'city' => $city,
                ];
            }

            return null;
        });
    }
}
