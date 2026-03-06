@extends('layouts.app')

@section('content')
    <h3>{{ __('favorites') }}</h3>

    @if($favorites->isEmpty())
        <div class="alert alert-secondary">You have no favorite movies yet.</div>
    @else
        <div id="favorites" class="row">
            @foreach($favorites as $f)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 position-relative" data-imdb="{{ $f->imdb_id }}">
                        <img class="card-img-top" src="{{ $f->poster }}" loading="lazy" alt="poster">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $f->title }}</h5>
                            <p class="card-text text-muted">{{ $f->year }}</p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge text-bg-success">Favorited</span>
                                <button class="btn btn-sm btn-danger del-btn" data-imdb="{{ $f->imdb_id }}">Remove</button>
                            </div>
                            <a href="{{ route('movies.show', $f->imdb_id) }}" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @push('scripts')
        <script>
            const translations = {
                no_favorites: "You have no favorite movies yet.",
                director: "Director",
                actors: "Actors",
                detail: "Detail"
            };

            document.getElementById('favorites') && document.getElementById('favorites').addEventListener('click', function (e) {
                // delete handling
                if (e.target.classList.contains('del-btn')) {
                    const btn = e.target;
                    const imdb = btn.dataset.imdb;
                    window.axios.delete('/favorites/' + imdb).then(() => {
                        const col = btn.closest('.col-md-4');
                        if (col) col.remove();
                        if (!document.querySelectorAll('#favorites .col-md-4').length) {
                            document.getElementById('favorites').innerHTML = `<div class="col-12"><div class="alert alert-secondary">${translations.no_favorites}</div></div>`;
                        }
                    });
                    e.stopPropagation();
                    return;
                }

                // open modal when clicking card (ignore clicks on buttons)
                const card = e.target.closest('.card[data-imdb]');
                if (card) {
                    const imdb = card.getAttribute('data-imdb');
                    window.axios.get('/movies/' + imdb).then(r => {
                        // use same modal from layout
                        const movie = r.data;
                        const body = document.getElementById('movieModalBody');
                        const titleEl = document.getElementById('movieModalLabel');
                        titleEl.textContent = movie.Title || translations.detail;
                        const poster = movie.Poster && movie.Poster !== 'N/A' ? `<img src="${movie.Poster}" class="img-fluid mb-3" loading="lazy">` : '';
                        let html = `<div class="row"><div class="col-md-4">${poster}</div><div class="col-md-8"><p class="text-muted">${movie.Year || ''} — ${movie.Genre || ''}</p><p>${movie.Plot || ''}</p><p><strong>${translations.director}:</strong> ${movie.Director || ''}</p><p><strong>${translations.actors}:</strong> ${movie.Actors || ''}</p></div></div>`;
                        body.innerHTML = html;
                        const modalEl = document.getElementById('movieModal');
                        if (modalEl && window.bootstrap) {
                            const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.show();
                        }
                    }).catch(() => { });
                }
            });
        </script>
    @endpush

@endsection