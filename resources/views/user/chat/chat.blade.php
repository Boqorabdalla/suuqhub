@extends('layouts.frontend')
@push('title', get_phrase('Chat with ') . $otherUser->name)
@section('frontend_layout')
    
    <section class="mb-4">
        <div class="container">
            <div class="row gx-20px">
                <div class="col-lg-4 col-xl-3">
                    @include('user.navigation')
                </div>
                <div class="col-lg-8 col-xl-9">
                    <div class="card border-0 shadow-sm">
                        <!-- Chat Header -->
                        <div class="card-header bg-white py-3 d-flex align-items-center gap-3">
                            <a href="{{ route('user.conversations', ['prefix' => $user_prefix]) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                            <img src="{{ get_user_image($otherUser->image, 'users/') }}" alt="" class="rounded-circle" width="40" height="40">
                            <div>
                                <h6 class="mb-0">{{ $otherUser->name }}</h6>
                                <small class="text-muted">{{ $otherUser->email }}</small>
                            </div>
                        </div>
                        
                        <!-- Messages -->
                        <div class="card-body chat-messages" style="height: 400px; overflow-y: auto;" id="chatContainer">
                            @foreach($messages as $message)
                                <div class="d-flex mb-3 {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="{{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }} rounded px-3 py-2" style="max-width: 70%;">
                                        <p class="mb-0">{{ $message->message }}</p>
                                        <small class="{{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->created_at->format('h:i A') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Message Input -->
                        <div class="card-footer bg-white">
                            <form action="{{ route('user.chat.send', ['prefix' => $user_prefix, 'userId' => $otherUserId]) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="text" name="message" class="form-control" placeholder="{{ get_phrase('Type a message...') }}" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Auto-scroll to bottom
        document.addEventListener('DOMContentLoaded', function() {
            var container = document.getElementById('chatContainer');
            container.scrollTop = container.scrollHeight;
        });
    </script>

@endsection