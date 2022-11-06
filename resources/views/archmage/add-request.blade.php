@extends('layouts.app', compact("title"))

@section('content')
    <form method="post" action={{ route("mod-request-back") }}>
        @csrf
        <h1>Dodaj nowe zapytanie</h1>
        <div id="request-box">
            <section class="input-group">
                <h2><i class="fa-solid fa-user"></i> Dane klienta</h2>
                <x-select name="client_id" label="Istniejący klient" :options="$clients" :empty-option="true" />
                <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" />
                <x-input type="email" name="email" label="Adres e-mail" />
                <x-input type="tel" name="phone" label="Numer telefonu" />
                <x-input type="text" name="other_medium" label="Inna forma kontaktu" />
                <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" />
                <script>
                function client_fields(){
                    const empty = $("#client_id").val() == "";
                    let cldata = {};

                    if(!empty){
                        $.ajax({
                            url: "{{ url('client_data') }}",
                            type: "get",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: $("#client_id").val()
                            },
                            success: function(res){
                                res = JSON.parse(res);
                                $("input", $("#client_id").parent().parent()).prop("disabled", true);
                                $("#client_name").val(res.client_name);
                                $("#email").val(res.email);
                                $("#phone").val(res.phone);
                                $("#other_medium").val(res.other_medium);
                                $("#contact_preference").val(res.contact_preference);
                                $("#wishes").html(res.default_wishes);
                                $("#special-prices-warning")
                                .html(`<i class="fa-solid fa-triangle-exclamation"></i> Klient ma specjalną wycenę:<br>${res.special_prices}`);
                            }
                        });
                    }else{
                        $("input", $("#client_id").parent().parent()).prop("disabled", false);
                        $("#client_name").val("");
                        $("#email").val("");
                        $("#phone").val("");
                        $("#other_medium").val("");
                        $("#contact_preference").val("");
                        $("#wishes").html("");
                        $("#special-prices-warning").html("");
                    }
                }
                $(document).ready(function(){
                    client_fields();
                    $("#client_id").change(function(){ client_fields() });
                });
                </script>
            </section>

            <section class="input-group">
                <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
                <x-select name="quest_type" label="Rodzaj zlecenia" :options="$questTypes" :required="true" />
                <x-input type="text" name="title" label="Tytuł utworu" />
                <x-input type="text" name="artist" label="Oryginalny wykonawca" />
                <x-input type="text" name="cover_artist" label="Coverujący" />
                <x-input type="text" name="link" label="Link do nagrania" />
                <x-input type="TEXT" name="wishes" label="Życzenia" />
            </section>

            <section class="input-group">
                <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
                <div id="special-prices-warning"></div>
                <x-input type="text" name="price_code" label="Wycena (kod i kwota)" :hint="$prices" />
                <div id="price-summary">
                    <div class="positions"></div>
                    <hr />
                    <div class="summary"><span>Razem:</span><span>0 zł</span></div>
                </div>
                <script>
                function calcPriceNow(){
                    const labels = $("#price_code").val();
                    const client_id = $("#client_id").val();
                    const positions_list = $("#price-summary .positions");
                    const sum_row = $("#price-summary .summary");
                    if(labels == "") positions_list.html(`<p class="grayed-out">podaj kategorie wyceny</p>`);
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
                    $("#price_code").change(function (e) { calcPriceNow() });
                });
                </script>
                <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" />
                <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" />
            </section>

            <section class="input-group">
                <h2><i class="fa-solid fa-calendar-days"></i> Grafik</h2>
                🚧 TBD 🚧
            </section>
        </div>
        <input type="hidden" name="modifying" value="0" />
        <button type="submit" class="hover-lift">
            <i class="fa-solid fa-paper-plane"></i> Popraw i oddaj do wyceny
        </button>
    </form>
@endsection
