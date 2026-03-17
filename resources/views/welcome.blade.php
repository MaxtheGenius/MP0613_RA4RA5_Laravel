{{-- Author: Maxime Pol Marcet --}}
@extends('layouts.master')

@section('title', 'Home - Laravel Films')

@section('content')
    <div class="text-center mb-5">
        <h1 class="display-4 font-weight-bold" style="letter-spacing: -1px;">Laravel Films</h1>
        <p style="font-size: 1.2rem; color: var(--text-secondary);">Manage your film collection easily.</p>
        {{-- I display cinema database connection info and film/actor counts --}}
        <div class="small text-muted mb-2">
            <strong>Database:</strong> {{ $dbName ?? '—' }}
            &nbsp;|&nbsp;
            <strong>Films:</strong> {{ $filmCount ?? 0 }}
            &nbsp;|&nbsp;
            <strong>Actors:</strong> {{ $actorCount ?? 0 }}
        </div>
    </div>

    @if(session('database_error'))
        <div class="alert alert-warning text-center mx-auto mb-4" style="max-width: 600px; border-radius: var(--radius-default);">
            {{ session('database_error') }}
        </div>
    @endif

    <div class="row justify-content-center">
        <!-- Navigation Menu -->
        <div class="col-md-8 mb-5">
            <div class="card-apple">
                <h3 class="mb-4">Main Menu</h3>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <x-button-apple class="m-2" :href="route('oldFilms')">Classic Films</x-button-apple>
                    <x-button-apple class="m-2" :href="route('newFilms')">New Releases</x-button-apple>
                    <x-button-apple class="m-2" :href="route('listFilms')">All Films</x-button-apple>
                    <x-button-apple class="m-2" :href="route('sortFilms')">Sorted by Year</x-button-apple>
                    <x-button-apple class="m-2" :href="route('countFilms')">Films Count</x-button-apple>
                    {{-- A hyperlink to the actors route was added here so that the actor listing is accessible from the welcome page; route('actors') is used so that the URL is kept in sync with the named route and the same styling as the other menu items is applied. --}}
                    <x-button-apple class="m-2" :href="route('actors')">All Actors</x-button-apple>
                    {{-- A hyperlink to the actor count view is added so that FR3 is accessible from the welcome page, mirroring the existing film count feature. --}}
                    <x-button-apple class="m-2" :href="route('countActors')">Actors Count</x-button-apple>
                </div>
            </div>
        </div>

        <!-- Actors by decade filter: this section was introduced so that FR2 can be initiated
             directly from the welcome view, allowing users to select a decade and navigate to
             the dedicated actor listing by decade while keeping the same visual style. -->
        <div class="col-md-8 mb-5">
            <div class="card-apple">
                <h3 class="mb-3 text-center">Filter Actors by Decade of Birth</h3>
                <form id="decade-form" method="GET" action="{{ url('actorout/actors/decade') }}">
                    <div class="form-row align-items-end">
                        <div class="col-md-8 form-group mb-3">
                            <label for="decade" class="font-weight-bold ml-1">Select decade</label>
                            <select id="decade" name="decade" class="form-control">
                                {{-- The available options are restricted to the supported decades (1980–2020)
                                     so that user input stays aligned with the validation rules enforced by
                                     the ValidateYear middleware for the FR2 route. --}}
                                <option value="">-- Select a decade --</option>
                                <option value="1980">1980s</option>
                                <option value="1990">1990s</option>
                                <option value="2000">2000s</option>
                                <option value="2010">2010s</option>
                                <option value="2020">2020s</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <x-button-apple type="submit" class="mt-2">
                                Show Actors
                            </x-button-apple>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Film Form -->
        <div class="col-md-8">
            <div class="card-apple">
                <h2 class="mb-4 text-center">Add New Film</h2>

                <!-- Errors block -->
                @if($errors->any())
                    <div class="alert alert-danger" style="border-radius: var(--radius-default); font-size: 0.95rem;">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Add film form -->
                <form action="{{ route('film') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="name" class="font-weight-bold ml-1">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                                placeholder="e.g. Titanic" required>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="year" class="font-weight-bold ml-1">Year</label>
                            <input type="number" name="year" id="year" class="form-control" value="{{ old('year') }}"
                                placeholder="e.g. 1997" required>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="genre" class="font-weight-bold ml-1">Genre</label>
                            <input type="text" name="genre" id="genre" class="form-control" value="{{ old('genre') }}"
                                placeholder="e.g. Drama" required>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="country" class="font-weight-bold ml-1">Country</label>
                            <input type="text" name="country" id="country" class="form-control" value="{{ old('country') }}"
                                placeholder="e.g. USA" required>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="duration" class="font-weight-bold ml-1">Duration (min)</label>
                            <input type="number" name="duration" id="duration" class="form-control"
                                value="{{ old('duration') }}" placeholder="e.g. 195" required>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="img_url" class="font-weight-bold ml-1">Poster URL</label>
                            <input type="url" name="img_url" id="img_url" class="form-control" value="{{ old('img_url') }}"
                                placeholder="https://..." required>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <x-button-apple type="submit">
                            Add Film
                        </x-button-apple>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('decade-form');
            if (!form) return;

            form.addEventListener('submit', function (event) {
                const select = document.getElementById('decade');
                if (!select || !select.value) {
                    // When no decade is selected, submission is cancelled so that the FR2 route
                    // is not called with an empty or invalid parameter.
                    event.preventDefault();
                    return;
                }

                // The URL for the FR2 route is built dynamically so that the selected decade is
                // passed as the {year} parameter expected by listActorsByDecade inside the
                // actorout group, while keeping the form method as a simple GET request.
                const baseUrl = "{{ url('actorout/actors/decade') }}";
                this.action = baseUrl + '/' + encodeURIComponent(select.value);
            });
        })();
    </script>
@endsection