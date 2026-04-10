<div class="col-md-4">
    <div class="astrologer-card">
        <div class="d-flex justify-content-between">
            <div class="d-flex gap-3">
                <img src="{{ $astrologer['image'] ?? asset('assets/images/default-profile.png') }}" class="profile-img">
                <div>
                    <h6 class="mb-1"><a href="{{ url('consultant/' . $astrologer['id']) }}">{{ $astrologer['display_name'] ?? $astrologer['name'] ?? 'Unknown' }}</a></h6>
                    <small>{{ is_array($astrologer['languages']) ? implode(', ', $astrologer['languages']) : $astrologer['languages'] ?? '' }}</small><br>
                    <div class="nhgd">
                        @if(isset($astrologer['skills']) && is_array($astrologer['skills']))
                            @php
                                $maxSkills = 3;
                                $skills = $astrologer['skills'];
                                $showMore = count($skills) > $maxSkills;
                            @endphp
                            @foreach(array_slice($skills, 0, $maxSkills) as $skill)
                                <span class="skill-badge">{{ $skill }}</span>
                            @endforeach
                            @if($showMore)
                                <span class="skill-badge more-skills-toggle" style="cursor:pointer;" onclick="this.nextElementSibling.classList.remove('d-none');this.classList.add('d-none')">+{{ count($skills) - $maxSkills }} more</span>
                                <span class="d-none more-skills-list">
                                    @foreach(array_slice($skills, $maxSkills) as $skill)
                                        <span class="skill-badge">{{ $skill }}</span>
                                    @endforeach
                                    <span class="skill-badge less-skills-toggle" style="cursor:pointer;" onclick="this.parentElement.classList.add('d-none');this.parentElement.previousElementSibling.classList.remove('d-none')">Show less</span>
                                </span>
                            @endif
                        @endif
                    </div>
                    <small>{{ $astrologer['experience'] ?? '' }}</small><br>
                    <span class="rating">⭐ {{ $astrologer['rating'] ?? '-' }} | {{ $astrologer['reviews'] ?? '0' }}</span>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="price">₹{{ $astrologer['price'] ?? '-' }}/Session</span>
            </div>
            <div class="d-flex gap-2">
                @php $isLoggedIn = session('auth.user') ? true : false; @endphp
                <button class="btn btn-danger mt-3" onclick="@if(!$isLoggedIn) showAuthModal(); @else window.location.href='{{ route('consultation') }}'; @endif">
                    <i class="fas fa-calendar-check"></i> Get an Appointment
                </button>
                <script>
                function showAuthModal() {
                    var modal = document.getElementById('authModal');
                    if (modal) {
                        var bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                        bsModal.show();
                    }
                }
                </script>
            </div>
        </div>
    </div>
</div>
