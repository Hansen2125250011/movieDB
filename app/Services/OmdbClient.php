<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class OmdbClient
{
    protected $client;
    protected $base = 'http://www.omdbapi.com/';

    public function __construct()
    {
        $this->client = new Client(['base_uri' => $this->base]);
    }

    public function search($q, $year = null, $type = null, $page = 1)
    {
        $key = 'omdb_search_' . md5($q . $year . $type . $page);
        return Cache::remember($key, 60, function () use ($q, $year, $type, $page) {
            $params = ['query' => ['apikey' => env('OMDB_API_KEY'), 's' => $q, 'page' => $page]];
            if ($year)
                $params['query']['y'] = $year;
            if ($type)
                $params['query']['type'] = $type;

            $res = $this->client->get('', $params);
            $body = json_decode((string) $res->getBody(), true);
            return $body;
        });
    }

    public function getById($imdb)
    {
        $key = 'omdb_id_' . md5($imdb);
        return Cache::remember($key, 60, function () use ($imdb) {
            $res = $this->client->get('', ['query' => ['apikey' => env('OMDB_API_KEY'), 'i' => $imdb, 'plot' => 'full']]);
            return json_decode((string) $res->getBody(), true);
        });
    }
}
