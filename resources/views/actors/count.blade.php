{{--
    Author: Maxime Pol Marcet

    This view was created to implement FR3 by providing a dedicated page that
    displays how many actors are stored in the database. The count value is
    computed in the controller via Eloquent (Actor::count()) and is presented
    here in a clear, minimal format so that the information is immediately
    understandable and the page remains consistent with the application's UI.
--}}
@extends('layouts.master')

@section('title', $title)

@section('content')
    <div class="text-center mb-5">
        <h1 class="mb-2">{{ $title }}</h1>
        <p class="text-secondary mb-0">Total actors stored in the database</p>
    </div>

    <div class="card-apple text-center py-5">
        <div class="display-4 font-weight-bold" style="letter-spacing: -1px;">
            {{ $count }}
        </div>
        <div class="text-secondary mt-2">
            actors
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="/" class="btn-apple">← Back to Home</a>
    </div>
@endsection

