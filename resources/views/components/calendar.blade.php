<table>
    <thead>
        <tr>
            <th>Dzień</th>
            <th>ReQuesty</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($calendar as $date => $meta)
        <tr class="cal-row {{ $meta['day_type'] }} {{ $meta['suggest_date'] ? 'suggest' : '' }}" date="{{ $meta['date_val'] }}">
            <td>{{ $date }}</td>
            <td>
                @foreach ($meta["quests"] as $quest)
                <a class="quest" href="{{ route('quest', ['id' => $quest->id]) }}" target="_blank" >
                    {{ $quest->song->title ?? "bez tytułu" }}
                </a>
                @endforeach

                @foreach ($meta["requests"] as $request)
                <a class="request ghost" href="{{ route('request', ['id' => $request->id]) }}" target="_blank" >
                    {{ $request->title ?? "bez tytułu" }}
                </a>
                @endforeach
            </td>
            {{-- <span class="grayed-out">pusto</span> --}}
        </tr>
    @endforeach
    </tbody>
</table>

<script>
$(document).ready(function(){
    $("tr[date]").click((el)=>{
        $("#deadline").val($(el.currentTarget).attr("date"));
    });
});
</script>
