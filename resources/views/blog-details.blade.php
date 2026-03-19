@extends('layouts.app')

@section('title', $blog['title'] ?? 'Blog Details')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="blog-details-card p-4 bg-white rounded shadow-sm">
                <h1 class="mb-3">{{ $blog['title'] ?? '' }}</h1>
                <div class="mb-3 text-muted small">
                    <span>By {{ $blog['author'] ?? 'Unknown' }}</span>
                    @if(!empty($blog['published_at']))
                        | <span>{{ \Carbon\Carbon::parse($blog['published_at'])->format('D, M d, Y') }}</span>
                    @endif
                </div>
                @if(!empty($blog['image']))
                    <img src="{{ $blog['image'] }}" alt="{{ $blog['title'] ?? '' }}" class="img-fluid rounded mb-4">
                @endif
                <div class="blog-details-body">
                    {!! $blog['content'] ?? '<p>No content available.</p>' !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
