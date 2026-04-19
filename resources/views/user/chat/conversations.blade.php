@extends('layouts.frontend')
@push('title', get_phrase('Messages'))
@section('frontend_layout')
    
    <section class="mb-4">
        <div class="container">
            <div class="row gx-20px">
                <div class="col-lg-4 col-xl-3">
                    @include('user.navigation')
                </div>
                <div class="col-lg-8 col-xl-9">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">{{ get_phrase('Messages') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @if(count($conversations) > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($conversations as $conv)
                                        <a href="{{ route('user.chat', $conv['user']->id) }}" class="list-group-item list-group-item-action d-flex gap-3 py-3">
                                            <img src="{{ get_user_image($conv['user']->image, 'users/') }}" alt="" class="rounded-circle" width="50" height="50">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-1">{{ $conv['user']->name }}</h6>
                                                    <small class="text-muted">{{ $conv['last_message']->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0 text-muted text-truncate" style="max-width: 200px;">
                                                    {{ $conv['last_message']->message }}
                                                </p>
                                            </div>
                                            @if($conv['unread_count'] > 0)
                                                <span class="badge bg-primary rounded-pill">{{ $conv['unread_count'] }}</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-chat-dots fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">{{ get_phrase('No conversations yet') }}</p>
                                    <a href="{{ route('beauty') }}" class="btn btn-primary">{{ get_phrase('Browse Listings') }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection