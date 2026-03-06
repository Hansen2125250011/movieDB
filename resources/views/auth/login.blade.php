@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">{{ __('login') }}</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="mb-3">
                            <label for="name">Username</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required
                                autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="password">Password</label>
                            <input id="password" type="password" class="form-control" name="password" required>
                        </div>

                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-primary">{{ __('login') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection