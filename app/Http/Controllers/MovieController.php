<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OmdbClient;

class MovieController extends Controller
{
    protected $omdb;

    public function __construct(OmdbClient $omdb)
    {
        $this->omdb = $omdb;
    }

    public function index(Request $request)
    {
        $q = (string) $request->get('q', '');
        $year = (string) $request->get('year', '');
        $type = (string) $request->get('type', '');
        $page = $request->get('page', 1);

        $results = null;
        if ($q !== '' || $year !== '' || $type !== '') {
            $apiQ = trim($q);
            if ($apiQ === '') {
                $apiQ = $type === 'series' ? 'series' : 'movie';
            }
            $results = $this->omdb->search($apiQ, $year, $type, $page);

            // If the generic fallback produced an error or too many results, try common terms
            if ($q === '' && isset($results['Error']) && ($results['Error'] === 'Too many results.' || str_contains($results['Error'], 'Conversion failed') || $results['Error'] === 'Incorrect IMDb ID.')) {
                $fallbacks = ['man', 'love', 'day', 'war', 'time', 'star', 'the'];
                foreach ($fallbacks as $fb) {
                    if ($fb === $apiQ)
                        continue;
                    $results = $this->omdb->search($fb, $year, $type, $page);
                    if (!isset($results['Error']) || ($results['Error'] !== 'Too many results.' && !str_contains($results['Error'], 'Conversion failed') && $results['Error'] !== 'Incorrect IMDb ID.')) {
                        break;
                    }
                }
            }
        }
        $favorites = [];
        if (auth()->check()) {
            $favorites = auth()->user()->favorites()->pluck('imdb_id')->toArray();
        }

        if ($request->ajax() || $request->wantsJson()) {
            $payload = $results ?: [];
            $payload['favorites'] = $favorites;
            return response()->json($payload);
        }

        return view('movies.index', ['results' => $results, 'q' => $q, 'year' => $year, 'type' => $type, 'favorites' => $favorites]);
    }

    public function show($imdb, Request $request)
    {
        $movie = $this->omdb->getById($imdb);
        $is_favorite = false;
        if (auth()->check()) {
            $is_favorite = auth()->user()->favorites()->where('imdb_id', $imdb)->exists();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($movie);
        }
        return view('movies.show', ['movie' => $movie, 'is_favorite' => $is_favorite]);
    }
}
