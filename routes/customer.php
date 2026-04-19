<?php

use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Agent\AgentSubscriptionController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\User\ChatController;
use Illuminate\Support\Facades\Route;

Route::prefix('{prefix}')->controller(CustomerController::class)->middleware('auth')->group(function () {
    Route::get('/messages/{id?}/{code?}', 'user_messages')->name('user.messages');
    Route::post('/messages/{code?}', 'send_message')->name('user.message.send');
});

Route::controller(CustomerController::class)->middleware('auth', 'customer')->group(function () {
    Route::get('/customer/wishlist', 'wishlist')->name('customer.wishlist');
    Route::get('/customer/remove/wishlist/{id}', 'remove_wishlist')->name('customer.remove.wishlist');
    Route::get('/customer/appointment', 'appointment')->name('customer.appointment');
    Route::get('/customer/become-an-agent', 'become_an_agent')->name('customer.become_an_agent');
    Route::get('/customer/following-agent', 'following_agent')->name('customer.following-agent');
    Route::get('/customer/following-remove/{id}', 'following_agent_remove')->name('customer.remove.follow_agent');
    Route::get('/customer/appointment/details/{id}/{type}', 'customer_appointment_view_details')->name('customer.appointment.view_details');
    Route::get('/customer/appointment/status/{id}', [AgentController::class, 'appointment_delete'])->name('customer.appointment.delete');
    Route::get('/agent/appointment/details/{id}/{type}', [AgentController::class, 'agent_appointment_view_details'])->name('agent.appointment.view_details');
});

// Chat Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/conversations', [ChatController::class, 'conversations'])->name('user.conversations');
    Route::get('/chat/{userId}', [ChatController::class, 'chat'])->name('user.chat');
    Route::post('/chat/{userId}', [ChatController::class, 'sendMessage'])->name('user.chat.send');
});

// Shop Subscription Route (only for agents)
Route::middleware(['auth', 'canAccessShop'])->group(function () {
    Route::get('/shop-subscription', [AgentSubscriptionController::class, 'index'])->name('shop.subscription');
    Route::post('/shop-subscription/subscribe/{plan}', [AgentSubscriptionController::class, 'subscribe'])->name('shop.subscription.subscribe');
    Route::post('/shop-subscription/cancel', [AgentSubscriptionController::class, 'cancel'])->name('shop.subscription.cancel');
    Route::post('/shop-subscription/upgrade/{plan}', [AgentSubscriptionController::class, 'upgrade'])->name('shop.subscription.upgrade');
});
