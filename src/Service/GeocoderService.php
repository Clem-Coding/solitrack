<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocoderService
{


    public function __construct(private HttpClientInterface $client) {}

    /**
     * Geocode a postal code to get the latitude, longitude and city name.
     * @param string $zipcode
     * @return array|null
     */
    public function geocodeByPostalCode(string $zipcode): ?array
    {

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
    }
}
