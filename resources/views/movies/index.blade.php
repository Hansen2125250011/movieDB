@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>{{ __('movies') }}</h3>
        <form id="search-form" class="row g-2">
            <div class="col-auto">
                <input type="text" name="q" id="q" class="form-control" placeholder="{{ __('search_placeholder') }}"
                    value="{{ old('q', $q ?? '') }}">
            </div>
            <div class="col-auto">
                <input type="text" name="year" id="year" class="form-control" placeholder="{{ __('year_placeholder') }}"
                    value="{{ old('year', $year ?? '') }}">
            </div>
            <div class="col-auto">
                <select name="type" id="type" class="form-control">
                    <option value="">{{ __('type_any') }}</option>
                    <option value="movie" {{ (old('type', $type ?? '') == 'movie') ? 'selected' : '' }}>{{ __('type_movie') }}
                    </option>
                    <option value="series" {{ (old('type', $type ?? '') == 'series') ? 'selected' : '' }}>
                        {{ __('type_series') }}
                    </option>
                    <option value="episode" {{ (old('type', $type ?? '') == 'episode') ? 'selected' : '' }}>
                        {{ __('type_episode') }}
                    </option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-secondary" type="submit">{{ __('search_btn') }}</button>
            </div>
        </form>
    </div>

    <div id="results" class="row">
        @if(isset($results['Search']))
            @foreach($results['Search'] as $item)
                @php $isFav = in_array($item['imdbID'], $favorites ?? []); @endphp
                <div class="col-md-4 mb-4">
                    <div class="card h-100 position-relative" data-imdb="{{ $item['imdbID'] }}">
                        <img class="card-img-top" src="{{ $item['Poster'] }}" loading="lazy" alt="poster">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $item['Title'] }}</h5>
                            <p class="card-text text-muted">{{ $item['Year'] }} — {{ ucfirst($item['Type'] ?? '') }}</p>
                            <div class="mt-auto d-flex justify-content-between align-items-center position-relative"
                                style="z-index: 2;">
                                <button class="btn btn-sm {{ $isFav ? 'btn-success' : 'btn-outline-primary' }} fav-btn"
                                    data-imdb="{{ $item['imdbID'] }}" data-title="{{ $item['Title'] }}"
                                    data-poster="{{ $item['Poster'] }}"
                                    data-year="{{ $item['Year'] }}">{{ $isFav ? __('favorited') : __('add_favorite') }}</button>
                            </div>
                            <a href="{{ route('movies.show', $item['imdbID']) }}" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div id="loading" class="text-center my-3" style="display:none;">Loading...</div>

    @push('scripts')
        <script>
            const translations = {
                add_favorite: "{{ __('add_favorite') }}",
                favorited: "{{ __('favorited') }}",
                loading: "{{ __('loading') }}",
                director: "{{ __('director') }}",
                actors: "{{ __('actors') }}",
                detail: "{{ __('detail') }}"
            };

            const resultsEl = document.getElementById('results');
            const loadingEl = document.getElementById('loading');
            let page = 0; // start at 0, loadNext increments to 1
            let q = document.getElementById('q').value || '';
            let year = document.getElementById('year').value || '';
            let type = document.getElementById('type').value || '';
            let favs = {!! json_encode($favorites ?? []) !!};
            let observerStarted = false;
            let sentinel, io;

            function appendItems(items, favList) {
                items.forEach(item => {
                    const isFav = (favList || favs || []).indexOf(item.imdbID) !== -1;
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-4';
                    col.innerHTML = `<div class="card h-100 position-relative" data-imdb="${item.imdbID}"><img class="card-img-top" src="${item.Poster}" loading="lazy"><div class="card-body d-flex flex-column"><h5 class="card-title">${item.Title}</h5><p class="card-text text-muted">${item.Year}</p><div class="mt-auto d-flex justify-content-between align-items-center position-relative" style="z-index: 2;"><button class="btn btn-sm ${isFav ? 'btn-success' : 'btn-outline-primary'} fav-btn" data-imdb="${item.imdbID}" data-title="${item.Title}" data-poster="${item.Poster}" data-year="${item.Year}">${isFav ? translations.favorited : translations.add_favorite}</button></div><a href="/movies/${item.imdbID}" class="stretched-link"></a></div></div>`;
                    resultsEl.appendChild(col);
                });
            }

            function showMovieModal(movie) {
                const body = document.getElementById('movieModalBody');
                const titleEl = document.getElementById('movieModalLabel');
                titleEl.textContent = movie.Title || translations.detail;
                const poster = movie.Poster && movie.Poster !== 'N/A' ? `<img src="${movie.Poster}" class="img-fluid mb-3" loading="lazy">` : '';
                let html = `<div class="row"><div class="col-md-4">${poster}</div><div class="col-md-8"><p class="text-muted">${movie.Year || ''} — ${movie.Genre || ''}</p><p>${movie.Plot || ''}</p><p><strong>${translations.director}:</strong> ${movie.Director || ''}</p><p><strong>${translations.actors}:</strong> ${movie.Actors || ''}</p></div></div>`;
                const alreadyFav = (favs.indexOf(movie.imdbID) !== -1);
                html += `<div class="mt-3"><button id="modal-fav-btn" class="btn ${alreadyFav ? 'btn-success' : 'btn-primary'}">${alreadyFav ? translations.favorited : translations.add_favorite}</button></div>`;
                body.innerHTML = html;
                // attach fav handler
                const modalFav = document.getElementById('modal-fav-btn');
                modalFav && modalFav.addEventListener('click', function () {
                    const imdb = movie.imdbID;
                    if (favs.indexOf(imdb) !== -1) {
                        // currently favorited -> unfavorite
                        window.axios.delete('/favorites/' + imdb).then(() => {
                            modalFav.textContent = translations.add_favorite;
                            modalFav.classList.remove('btn-success');
                            modalFav.classList.add('btn-primary');
                            const cardBtn = document.querySelector(`.card[data-imdb="${imdb}"] .fav-btn`);
                            if (cardBtn) {
                                cardBtn.textContent = translations.add_favorite;
                                cardBtn.classList.remove('btn-success');
                                cardBtn.classList.add('btn-outline-primary');
                            }
                            const idx = favs.indexOf(imdb); if (idx !== -1) favs.splice(idx, 1);
                        }).catch(() => { });
                    } else {
                        const payload = { imdb_id: imdb, title: movie.Title, poster: movie.Poster, year: movie.Year };
                        window.axios.post('/favorites', payload).then(() => {
                            modalFav.textContent = translations.favorited;
                            modalFav.classList.remove('btn-primary');
                            modalFav.classList.add('btn-success');
                            const cardBtn = document.querySelector(`.card[data-imdb="${imdb}"] .fav-btn`);
                            if (cardBtn) {
                                cardBtn.textContent = translations.favorited;
                                cardBtn.classList.remove('btn-outline-primary');
                                cardBtn.classList.add('btn-success');
                            }
                            if (favs.indexOf(imdb) === -1) favs.push(imdb);
                        }).catch(() => { });
                    }
                });
                // show modal (Bootstrap 5 vanilla JS)
                const modalEl = document.getElementById('movieModal');
                if (modalEl && window.bootstrap) {
                    const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            }

            function startObserver() {
                if (observerStarted) return;
                sentinel = document.createElement('div');
                resultsEl.after(sentinel);
                io = new IntersectionObserver(entries => { if (entries[0].isIntersecting) loadNext(); }, { rootMargin: '200px' });
                io.observe(sentinel);
                observerStarted = true;
            }

            function loadNext() {
                if (!q && !year && !type) return;
                page++;
                loadingEl.style.display = '';
                window.axios.get('/movies', { params: { q: q, year: year, type: type, page: page } }).then(r => {
                    loadingEl.style.display = 'none';
                    if (r.data && r.data.Search) {
                        const newFavs = r.data.favorites || favs;
                        appendItems(r.data.Search, newFavs);
                    } else if (page === 1) {
                        resultsEl.innerHTML = `<div class="col-12 text-center text-muted py-5">${r.data.Error || 'No results found.'}</div>`;
                    }
                }).catch(() => { loadingEl.style.display = 'none'; });
            }

            // Observer is started after first search via `startObserver()`

            const searchForm = document.getElementById('search-form');
            const typeSelect = document.getElementById('type');
            const yearInput = document.getElementById('year');

            function triggerSearchIfValid() {
                const queryVal = document.getElementById('q').value.trim();
                const yearVal = yearInput.value.trim();
                const typeVal = typeSelect.value;
                if (queryVal || yearVal || typeVal) {
                    searchForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }

            typeSelect.addEventListener('change', triggerSearchIfValid);
            yearInput.addEventListener('change', triggerSearchIfValid);

            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                q = document.getElementById('q').value.trim();
                year = yearInput.value.trim();
                type = typeSelect.value;
                resultsEl.innerHTML = '';
                page = 0;
                if (q || year || type) {
                    // update URL to reflect search filters
                    try {
                        const params = new URLSearchParams();
                        if (q) params.set('q', q);
                        if (year) params.set('year', year);
                        if (type) params.set('type', type);
                        const newUrl = '/movies?' + params.toString();
                        history.replaceState({}, '', newUrl);
                    } catch (err) { }
                    loadNext();
                    startObserver();
                } else {
                    alert("{{ __('search_placeholder') }}");
                }
            });

            // If page loaded with a query already (server-rendered results), initialize observer
            if (q || year || type) {
                // set page to 1 because server may have rendered first page
                page = 1;
                startObserver();
            }

            resultsEl.addEventListener('click', function (e) {
                // favorite button handling
                if (e.target.classList.contains('fav-btn')) {
                    const btn = e.target;
                    const imdb = btn.dataset.imdb;
                    // toggle: if already favorited, send DELETE, else POST
                    if (favs.indexOf(imdb) !== -1) {
                        window.axios.delete('/favorites/' + imdb).then(() => {
                            btn.textContent = translations.add_favorite;
                            btn.classList.remove('btn-success');
                            btn.classList.add('btn-outline-primary');
                            const idx = favs.indexOf(imdb); if (idx !== -1) favs.splice(idx, 1);
                            // update modal button if open
                            const modalBtn = document.getElementById('modal-fav-btn');
                            if (modalBtn && modalBtn.textContent) { modalBtn.textContent = translations.add_favorite; modalBtn.classList.remove('btn-success'); modalBtn.classList.add('btn-primary'); }
                        }).catch(() => { });
                    } else {
                        const payload = { imdb_id: imdb, title: btn.dataset.title, poster: btn.dataset.poster, year: btn.dataset.year };
                        window.axios.post('/favorites', payload).then(() => {
                            btn.textContent = translations.favorited;
                            btn.classList.remove('btn-outline-primary');
                            btn.classList.add('btn-success');
                            const idx = favs.indexOf(imdb); if (idx === -1) favs.push(imdb);
                            // update modal button if open
                            const modalBtn = document.getElementById('modal-fav-btn');
                            if (modalBtn && modalBtn.textContent) { modalBtn.textContent = translations.favorited; modalBtn.classList.remove('btn-primary'); modalBtn.classList.add('btn-success'); }
                        }).catch(() => { });
                    }
                    e.stopPropagation();
                    return;
                }

                // open modal when card clicked (ignore clicks on buttons/links)
                const card = e.target.closest('.card[data-imdb]');
                if (card) {
                    const imdb = card.getAttribute('data-imdb');
                    // fetch detail
                    window.axios.get('/movies/' + imdb).then(r => {
                        showMovieModal(r.data);
                    }).catch(() => { });
                }
            });
        </script>
    @endpush

@endsection