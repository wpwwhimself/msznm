@extends('layouts.app', compact("title"))

@section('content')
    @foreach (["success", "error"] as $status)
        @if (session($status))
            <x-alert :status="$status" />
        @endif
    @endforeach

    <section id="who-am-i">
        <h2>Zalogowano jako {{ Auth::user()->client->clientsName ?? "🧙‍♂️" }}</h2>
    </section>

    <section id="dashboard-requests">
        <div class="section-header">
            <h1><i class="fa-solid fa-envelope"></i> Zapytania</h1>
            <div>
                <a href="{{ route("quests") }}">Wszystkie <i class="fa-solid fa-angles-right"></i></a>
                <a href="{{ route("add-request") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
            </div>
        </div>
        <div class="dashboard-mini-wrapper">
        @if (!count($requests))
            <p class="grayed-out">brak aktywnych zapytań</p>
        @else
            @foreach ($requests as $request)
                <x-request-mini :request="$request" />
            @endforeach
        @endif
        </div>
    </section>

    <section id="dashboard-quests">
        <div class="section-header">
            <h1><i class="fa-solid fa-gears"></i> Aktualne zlecenia</h1>
            <div>
                <a href="{{ route("quests") }}">Wszystkie <i class="fa-solid fa-angles-right"></i></a>
            </div>
        </div>
        <div class="dashboard-mini-wrapper">
        @if (!count($requests))
            <p class="grayed-out">brak aktywnych zleceń</p>
        @else
            @foreach ($quests as $quest)
                <x-quest-mini :quest="$quest" />
            @endforeach
        @endif
        </div>
    </section>

    <section id="dashboard-finances">
        <div class="section-header">
            <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
        </div>
        <div class="dashboard-mini-wrapper">
            🚧 TBD 🚧
        </div>
    </section>
@endsection
