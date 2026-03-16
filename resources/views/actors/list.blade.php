{{--
    Author: Maxime Pol Marcet

    This view was created so that a dedicated list view exists for the Actor module
    and all actors stored in the database can be displayed in one place. It is
    placed under views/actors so that actor-related views are grouped and the
    structure can be extended with further actor views later. The required
    columns (id, photo/img_url, name, surname, birthdate, country) are rendered
    so that the acceptance criteria are met; data is received from the controller
    as Eloquent model instances and is rendered here via object properties.
--}}
@extends('layouts.master')

@section('title', $title)

@section('extra-styles')
    <style>
        .table-apple {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-apple th {
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            color: var(--text-secondary);
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-apple td {
            padding: 24px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            background: white;
            font-size: 15px;
        }

        .table-apple tr:last-child td {
            border-bottom: none;
        }

        .table-apple tr:hover td {
            background-color: #FAFAFA;
        }

        .actor-photo {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 56px;
            height: 56px;
            object-fit: cover;
            transition: transform 0.2s;
        }

        .actor-photo:hover {
            transform: scale(1.05);
        }

        .badge-country {
            background-color: #F2F2F7;
            color: var(--text-primary);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
    <div class="text-center mb-5">
        <h1 class="mb-2">{{ $title }}</h1>
        @if(!empty($actors))
            <p>Showing {{ count($actors) }} actors</p>
        @endif
    </div>

    {{-- An empty state is shown when no actors are passed, so that the page does not break and the user is informed. --}}
    @if(empty($actors))
        <div class="card-apple text-center py-5">
            <h3 class="text-secondary">No actors available</h3>
            <p class="mb-0">No actors were found in the database.</p>
        </div>
    @else
        {{-- All actors are rendered in a table so that the required columns are displayed; the same table and badge styles as the films list are used so that the UI is consistent across the application. --}}
        <div class="card-apple p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table-apple">
                    <thead>
                        <tr>
                            <th class="pl-4">Id</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>Birthdate</th>
                            <th>Country</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actors as $actor)
                            <tr>
                                <td class="pl-4 text-secondary">{{ $actor->id }}</td>
                                <td class="pl-2" width="90">
                                    <img src="{{ $actor->img_url }}" alt="{{ $actor->name }} {{ $actor->surname }}" class="actor-photo"
                                        onerror="this.src='{{ asset('img/image-not-found-placeholder.png') }}';" />
                                </td>
                                <td>
                                    <span class="font-weight-bold" style="font-size: 1.1rem;">{{ $actor->name }}</span>
                                </td>
                                <td>{{ $actor->surname }}</td>
                                <td class="text-secondary">{{ $actor->birthdate?->format('Y-m-d') ?? '—' }}</td>
                                <td>
                                    <span class="badge-country">{{ $actor->country }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- A link back to the home page is provided so that the user can return to the welcome view without using the browser back button. --}}
    <div class="text-center mt-5">
        <a href="/" class="btn-apple">← Back to Home</a>
    </div>
@endsection
