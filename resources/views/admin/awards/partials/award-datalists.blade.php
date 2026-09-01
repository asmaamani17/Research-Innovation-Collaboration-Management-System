<datalist id="staff-options">
    @foreach($staff as $item)
        <option value="{{ trim(($item->staff_id ?? '') . ' - ' . ($item->staff_name ?? '')) }}"></option>
    @endforeach
</datalist>

<datalist id="project-options">
    @foreach($projects as $item)
        <option value="{{ trim(($item->project_id ?? '') . ' - ' . ($item->project_title ?? '')) }}"></option>
    @endforeach
</datalist>

<datalist id="event-options">
    @foreach($events as $item)
        <option value="{{ $item->event_name }}"></option>
    @endforeach
</datalist>
