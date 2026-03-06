@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="row no-gutters">
            <div class="col-md-4">
                <img src="{{ $movie['Poster'] ?? '' }}" class="card-img" alt="poster" loading="lazy">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h3 class="card-title">{{ $movie['Title'] ?? 'Detail' }}</h3>
                    <p class="text-muted">{{ $movie['Year'] ?? '' }} — {{ $movie['Genre'] ?? '' }}</p>
                    <p>{{ $movie['Plot'] ?? '' }}</p>
                    <button id="fav-btn" class="btn {{ $is_favorite ? 'btn-success' : 'btn-outline-primary' }}"
                        data-imdb="{{ $movie['imdbID'] ?? '' }}" data-title="{{ $movie['Title'] ?? '' }}"
                        data-poster="{{ $movie['Poster'] ?? '' }}"
                        data-year="{{ $movie['Year'] ?? '' }}">{{ $is_favorite ? __('favorited') : __('add_favorite') }}</button>
                    <a href="javascript:history.back()" class="btn btn-link">Back</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const translations = {
                add_favorite: "{{ __('add_favorite') }}",
                favorited: "{{ __('favorited') }}"
            };

            let isFav = {{ $is_favorite ? 'true' : 'false' }};

            document.getElementById('fav-btn').addEventListener('click', function () {
                const btn = this;
                const imdb = btn.dataset.imdb;

                if (isFav) {
                    window.axios.delete('/favorites/' + imdb).then(() => {
                        isFav = false;
                        btn.textContent = translations.add_favorite;
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-primary');
                    }).catch(() => { });
                } else {
                    const payload = { imdb_id: imdb, title: btn.dataset.title, poster: btn.dataset.poster, year: btn.dataset.year };
                    window.axios.post('/favorites', payload).then(() => {
                        isFav = true;
                        btn.textContent = translations.favorited;
                        btn.classList.remove('btn-outline-primary');
                        btn.classList.add('btn-success');
                    }).catch(() => { });
                }
            });
        </script>
    @endpush

@endsection