<select name="adjacent_room_ids[]"
    id="adjacent-rooms-select"
    class="form-control select2"
    data-placeholder="Select rooms"
    multiple>
    @foreach ($rooms as $room)
    <option value="{{ $room->id }}">{{ $room->name }}</option>
    @endforeach
</select>