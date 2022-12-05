@extends('layouts.mail', [
    "title" => "Zlecenie zaktualizowane"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        wprowadziłem zmiany w {{ $pl["kobieta"] ? "Pani" : "Pana" }} zleceniu:
    </p>

    <x-quest-mini :quest="$quest" />

    <p>
        Aby przejrzeć szczegóły zlecenia, proszę o kliknięcie linku poniżej.
        Uprzejmie proszę o wyrażenie opinii lub ewentualnych uwag celem wprowadzenia poprawek.
    </p>

    <x-button
        label="Szczegóły" icon="circle-info"
        action="{{ route('quest', ['id' => $quest->id]) }}"
        />
@endsection
