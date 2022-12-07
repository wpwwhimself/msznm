@extends('layouts.app', ["title" => ($request->title ?? "bez tytułu") . " | $title"])

@section('content')
<p class="tutorial"><i class="fa-solid fa-circle-question"></i>
@switch($request->status_id)
    @case(1)
        Twoje zapytanie zostało wysłane. W&nbsp;najbliższym czasie (może nawet jutro) odniosę się do niego i&nbsp;przygotuję odpowiednią wycenę. Zostaniesz o&nbsp;tym poinformowany w&nbsp;wybrany przez Ciebie sposób.</p>
        @break
    @case(5)
        Wyceniłem Twoje zapytanie. Możesz potwierdzić przedstawione warunki lub – jeśli się z nimi nie zgadzasz – poprawić odpowiednie pola i przesłać mi do ponownej wyceny. W ostateczności możesz odrzucić zapytanie.
        @break
    @case(6)
        Twoje poprawki zostały przekazane. Odniosę się do nich i przedstawię poprawioną wycenę.
        @break
    @case(7)
        Nie podejmę się wykonania tego zlecenia. Prawdopobodmie jest ono dla mnie niewykonalne.
        @break
    @case(9)
        Zapytanie zostało przyjęte. Utworzyłem zlecenie, do którego link znajdziesz poniżej.
        @break
@endswitch
</p>

<form method="POST" action="{{ route("mod-request-back") }}">
    @csrf
    <script>
    $(document).ready(function(){
        //disabling inputs if no change is allowed
        if([1, 6, 7, 8, 9].includes(parseInt($(".quest-phase").attr("status")))){
            $("button, .submit").hide();
            $("input, textarea, select").prop("disabled", true);
        };
    });
    </script>
    <h1>Szczegóły zapytania</h1>
    <x-phase-indicator :status-id="$request->status_id" />
    @if ($request->quest_id)
    <h2>Zlecenie przepisane z numerem {{ $request->quest_id }}. <x-a href="{{ route('quest', ['id' => $request->quest_id]) }}">Przejdź do zlecenia</x-a></h2>
    @endif
    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
            <x-select name="quest_type" label="Rodzaj zlecenia" :small="true" :options="$questTypes" :required="true" value="{{ $request->quest_type_id }}" />
            <x-input type="text" name="title" label="Tytuł utworu" value="{{ $request->title }}" />
            <x-input type="text" name="artist" label="Wykonawca" value="{{ $request->artist }}" />
            <x-input type="url" name="link" label="Link do nagrania" :small="true" value="{{ $request->link }}" />
            <x-link-interpreter :raw="$request->link" />
            <x-input type="TEXT" name="wishes" label="Życzenia" value="{{ $request->wishes }}" />
            <x-input type="date" name="hard_deadline" label="Twój termin wykonania" value="{{ $request->hard_deadline }}" />
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            @unless ($request->price)
            <p class="yellowed-out"><i class="fa-solid fa-hourglass-half fa-fade"></i> pojawi się w ciągu najbliższych dni</p>
            @endunless
            <div id="price-summary" class="hint-table">
                <div class="positions"></div>
                <hr />
                <div class="summary"><span>Razem:</span><span>0 zł</span></div>
            </div>
            <script>
            function calcPriceNow(){
                const labels = "{{ $request->price_code }}";
                const client_id = {!! $request->client_id ?? "\"\"" !!};
                const positions_list = $("#price-summary .positions");
                const sum_row = $("#price-summary .summary");
                if(labels == "") $("#price-summary").hide();
                else{
                    $.ajax({
                        url: "{{ url('price_calc') }}",
                        type: "post",
                        data: {
                            _token: '{{ csrf_token() }}',
                            labels: labels,
                            client_id: client_id
                        },
                        success: function(res){
                            let content = ``;
                            for(line of res[1]){
                                content += `<span>${line[0]}</span><span>${line[1]}</span>`;
                            }
                            positions_list.html(content);
                            sum_row.html(`<span>Razem:</span><span>${res[0]} zł</span>`);
                            if(res[2]) positions_list.addClass("overridden");
                                else positions_list.removeClass("overridden");
                        }
                    });
                }
            }
            $(document).ready(function(){
                calcPriceNow();
            });
            </script>
            @if ($request->deadline)
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $request->deadline }}" />
            @endif
            @if ($request->price && $request->status_id == 5)
            <div class="tutorial">
                <p><i class="fa-solid fa-circle-question"></i> Termin oddania jest liczony do podanego dnia włącznie.</p>
                <p><i class="fa-solid fa-circle-question"></i> Opłaty projektu możesz dokonać na 2 sposoby:</p>
                <ul>
                    <li>na numer konta <b>53 1090 1607 0000 0001 1633 2919</b><br>
                        (w tytule ID zlecenia),</li>
                    <li>BLIKiem na numer telefonu <b>530 268 000</b>.</li>
                </ul>
                <p>Nie jest ona wymagana do przeglądania plików,<br>
                    ale będzie potrzebna do ich pobrania.</p>
                <p><i class="fa-solid fa-circle-question"></i> Pliki będą dostępne z poziomu tej strony internetowej.</p>
            </div>
            @endif
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$request" />
        </section>
    </div>
    <input type="hidden" name="modifying" value="{{ $request->id }}" />
    <input type="hidden" name="questioning" value="1" />
    <div class="flexright">
        <x-button
            label="Potwierdź" icon="9"
            action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}"
            />
        <x-button
            label="Poproś o ponowną wycenę" icon="6"
            action="submit"
            />
        <x-button
            label="Odrzuć" icon="8" :danger="true"
            action="{{ route('request-final', ['id' => $request->id, 'status' => 8]) }}"
            />
    </div>
</form>
@endsection
