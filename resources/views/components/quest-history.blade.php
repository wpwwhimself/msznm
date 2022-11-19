<div id="quest-history">
    @forelse ($history as $item)
    <div class="history-position p-{{ $item->new_status_id }} {{ $item->changed_by == 1 ? "by-me" : "by-client" }}">
        <span>
            <span class="client-name">{{ $clientName($item->changed_by) }}</span>
            <br>
            {!! $statusSymbol($item->new_status_id) !!} {{ $statusName($item->new_status_id) }}
            <ul>
            @foreach (json_decode($item->comment) ?? [] as $key=>$val)
            <li>{{ $key }}: {{ $val }}</li>
            @endforeach
            </ul>
        </span>
        <span>{!! str_replace(" ", "<br>", $item->date) !!}</span>
    </div>
    @empty
    <p class="grayed-out">historia tego zlecenia jest pusta</p>
    @endforelse
</div>