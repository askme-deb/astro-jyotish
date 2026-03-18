@if(!empty($astrologers) && is_array($astrologers))
    @foreach($astrologers as $astrologer)
        <x-astrologer-card :astrologer="$astrologer" />
    @endforeach
@else
    <div class="col-12">
        <div class="alert alert-warning text-center">No astrologers found.</div>
    </div>
@endif
