<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $favorites = $user->favorites()->latest()->get();
        return view('favorites.index', ['favorites' => $favorites]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'imdb_id' => 'required|string',
            'title' => 'required|string',
        ]);

        $user = Auth::user();

        $fav = $user->favorites()->firstOrCreate([
            'imdb_id' => $request->input('imdb_id')
        ], [
            'title' => $request->input('title'),
            'poster' => $request->input('poster'),
            'year' => $request->input('year'),
            'type' => $request->input('type')
        ]);

        return response()->json(['success' => true, 'favorite' => $fav]);
    }

    public function destroy($imdb)
    {
        $user = Auth::user();
        $fav = $user->favorites()->where('imdb_id', $imdb)->first();
        if ($fav) {
            $fav->delete();
        }
        return response()->json(['success' => true]);
    }
}
