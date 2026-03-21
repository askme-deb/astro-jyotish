@if(!empty($astrologers) && is_array($astrologers))
    @foreach($astrologers as $astrologer)
        @if(!isset($astrologer['name']) || $astrologer['name'] !== 'Raju Maharaj')
            <x-astrologer-card :astrologer="$astrologer" />
        @endif
    @endforeach
@else
    <div class="col-12">
        <div class="alert alert-warning text-center">No astrologers found.</div>
    </div>
@endif
