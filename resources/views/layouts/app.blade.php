<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Movie App</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Minimal fallback styles if external CSS fails to load */
        body {
            font-family: Nunito, Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #212529;
        }

        html,
        body {
            background-color: #f8fafc !important;
            color: #212529 !important;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        /* Mobile Navbar Adjustments */
        @media (max-width: 991.98px) {
            .navbar-nav {
                padding-top: 10px;
                border-top: 1px solid #eee;
                margin-top: 10px;
            }

            .navbar-nav:first-child {
                border-top: none;
                margin-top: 0;
                padding-top: 0;
            }

            .nav-item {
                padding: 5px 0;
            }

            .language-switcher {
                width: 100%;
                margin-top: 10px;
                margin-left: 0 !important;
            }

            .language-switcher .btn-group {
                display: flex;
                width: 100%;
            }

            .language-switcher .btn {
                flex: 1;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">
                <i class="fas fa-film me-1"></i> MovieApp
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="{{ route('movies.index') }}">
                                {{ __('movies') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="{{ route('favorites.index') }}">
                                {{ __('favorites') }}
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto align-items-lg-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="{{ route('login') }}">{{ __('login') }}</a>
                        </li>
                    @else
                        <li class="nav-item me-lg-3">
                            <span class="nav-link text-dark">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger fw-bold" href="#"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i> {{ __('logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    @endguest

                    <li class="nav-item ms-lg-3 language-switcher">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Language switch">
                            <a href="/lang/en"
                                class="btn btn-outline-primary {{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                            <a href="/lang/id"
                                class="btn btn-outline-primary {{ app()->getLocale() == 'id' ? 'active' : '' }}">ID</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('status'))
            <div class="alert alert-info">{{ session('status') }}</div>
        @endif
        @yield('content')
    </div>

    <!-- Movie detail modal -->
    <div class="modal fade" id="movieModal" tabindex="-1" role="dialog" aria-labelledby="movieModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="movieModalLabel">Movie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="movieModalBody">
                    <!-- Filled dynamically -->
                    <div class="text-center">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Load lightweight front-end libs via CDN (no npm required) -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Attach axios CSRF token if present
        if (window.axios && document.querySelector('meta[name="csrf-token"]')) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
    </script>
    @stack('scripts')
</body>

</html>