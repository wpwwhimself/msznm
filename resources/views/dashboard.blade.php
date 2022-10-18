@extends('layouts.app', compact("title", "extraCss"))

@section('content')
    @foreach (["success", "error"] as $status)
        @if (session($status))
            <div class="alert {{ $status }}">
                {{ session($status) }}
            </div>
        @endif
    @endforeach

    <section id="dashboard-requests">
        <div class="section-header">
            <h1>🎤 Zapytania</h1>
            <a href="{{ route("requests") }}">Wszystkie</a>
        </div>
        <div class="dashboard-mini-wrapper">
        @foreach ($requests as $request)
            <x-request-mini :request="$request" />
        @endforeach
        @if (!count($requests))
            <p class="grayed-out">— brak aktywnych zapytań —</p>
        @endif
        </div>
    </section>

    <section id="dashboard-quests">
        <div class="section-header">
            <h1>🎸 Aktualne zlecenia</h1>
            <a href="{{ route("quests") }}">Wszystkie</a>
        </div>
        <div class="dashboard-mini-wrapper">
        @foreach ($quests as $quest)
            <x-quest-mini :quest="$quest" />
        @endforeach
        @if (!count($requests))
            <p class="grayed-out">— brak aktywnych zleceń —</p>
        @endif
        </div>
    </section>

    <section id="dashboard-finances">
        <div class="section-header">
            <h1>💰 Finanse</h1>
        </div>
        <div class="dashboard-mini-wrapper">
            🚧 TBD 🚧
        </div>
    </section>
@endsection
