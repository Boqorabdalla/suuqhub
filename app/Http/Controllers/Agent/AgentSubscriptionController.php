<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ShopSubscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class AgentSubscriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $currentSubscription = ShopSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->with('plan')
            ->first();
        
        $subscriptionHistory = ShopSubscription::where('user_id', $user->id)
            ->with('plan')
            ->orderByDesc('created_at')
            ->paginate(10);
        
        $plans = SubscriptionPlan::active()->orderBySort()->get();
        
        return view('agent.subscriptions.index', compact(
            'currentSubscription', 
            'subscriptionHistory', 
            'plans'
        ));
    }

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $user = auth()->user();
        
        if ($request->payment_method === 'manual') {
            SubscriptionPayment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'payment_method' => 'manual',
                'status' => 'pending',
                'notes' => 'Awaiting admin approval',
            ]);
            
            return redirect()->back()->with('success', 'Your subscription request has been submitted and is awaiting admin approval.');
        }
        
        return redirect()->back()->with('error', 'Online payment integration coming soon.');
    }

    public function cancel()
    {
        $user = auth()->user();
        $subscription = ShopSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'auto_renew' => false,
                'cancelled_at' => now(),
            ]);
            
            return redirect()->back()->with('success', 'Your subscription has been cancelled.');
        }
        
        return redirect()->back()->with('error', 'No active subscription found.');
    }

    public function upgrade(SubscriptionPlan $plan)
    {
        $user = auth()->user();
        $currentPlan = ShopSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('plan')
            ->first();
        
        if (!$currentPlan) {
            return redirect()->route('shop.subscription')->with('error', 'No current subscription to upgrade from.');
        }
        
        $proratedAmount = max(0, $plan->price - $currentPlan->plan->price);
        
        if ($proratedAmount > 0) {
            SubscriptionPayment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $proratedAmount,
                'payment_method' => 'manual',
                'status' => 'pending',
                'notes' => 'Upgrade from ' . $currentPlan->plan->name . ' - Prorated amount',
            ]);
        } else {
            $currentPlan->update([
                'plan_id' => $plan->id,
            ]);
        }
        
        return redirect()->back()->with('success', 'Upgrade request submitted. You will be notified once approved.');
    }
}
