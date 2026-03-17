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
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">

            <h1 class="mb-5">{{ $title }}</h1>

            <div class="card-apple mb-5 py-5">
                <p class="text-uppercase text-muted font-weight-bold mb-2" style="letter-spacing: 1px;">
                    Total in Database
                </p>
                <div class="display-1 font-weight-bold text-dark mb-2" style="letter-spacing: -3px;">
                    {{ $count }}
                </div>
                <p class="h5 text-secondary font-weight-normal">Actors available</p>
            </div>

            <x-button-apple href="/">
                ← Back to Home
            </x-button-apple>

        </div>
    </div>
@endsection

