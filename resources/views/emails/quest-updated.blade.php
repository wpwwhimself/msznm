@extends('layouts.mail', [
    "title" => "Zlecenie zaktualizowane"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        wprowadziłem zmiany w {{ $pl["kobieta"] ? "Pani" : "Pana" }} zleceniu:
    </p>

    <x-mail-quest-mini :quest="$quest" />

    <h3>
        <a
            class="button"
            href="{{ route('quest', ['id' => $quest->id]) }}"
            >
            Szczegóły zlecenia
        </a>
    </h3>

    <p>
        Uprzejmie proszę o wyrażenie opinii lub ewentualnych uwag celem wprowadzenia poprawek.
    </p>
@endsection