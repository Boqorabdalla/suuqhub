<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Models\ShopSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopSubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::orderBySort()->get();
        return view('admin.subscriptions.plans', compact('plans'));
    }

    public function createPlan()
    {
        return view('admin.subscriptions.create_plan');
    }

    public function storePlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly,lifetime',
            'duration_days' => 'required|integer|min:1',
            'tier' => 'required|in:basic,standard,premium,enterprise',
            'max_listings' => 'required|integer|min:0',
            'max_products' => 'required|integer|min:0',
            'commission_rate' => 'required|integer|min:0|max:100',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['status'] = $request->has('status');
        $data['is_featured'] = $request->has('is_featured');
        $data['has_analytics'] = $request->has('has_analytics');
        $data['has_custom_branding'] = $request->has('has_custom_branding');
        $data['has_priority_support'] = $request->has('has_priority_support');
        $data['has_api_access'] = $request->has('has_api_access');

        SubscriptionPlan::create($data);

        return redirect()->route('admin.shop.subscriptions.plans')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function editPlan(SubscriptionPlan $plan)
    {
        return view('admin.subscriptions.edit_plan', compact('plan'));
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_period' => 'required|in:monthly,yearly,lifetime',
            'duration_days' => 'required|integer|min:1',
            'tier' => 'required|in:basic,standard,premium,enterprise',
            'max_listings' => 'required|integer|min:0',
            'max_products' => 'required|integer|min:0',
            'commission_rate' => 'required|integer|min:0|max:100',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['status'] = $request->has('status');
        $data['is_featured'] = $request->has('is_featured');
        $data['has_analytics'] = $request->has('has_analytics');
        $data['has_custom_branding'] = $request->has('has_custom_branding');
        $data['has_priority_support'] = $request->has('has_priority_support');
        $data['has_api_access'] = $request->has('has_api_access');

        $plan->update($data);

        return redirect()->route('admin.shop.subscriptions.plans')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroyPlan(SubscriptionPlan $plan)
    {
        $activeCount = ShopSubscription::where('plan_id', $plan->id)->where('status', 'active')->count();
        if ($activeCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete plan with active subscriptions.');
        }
        
        $plan->delete();
        return redirect()->route('admin.shop.subscriptions.plans')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    public function payments(Request $request)
    {
        $query = SubscriptionPayment::with(['user', 'plan']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderByDesc('created_at')->paginate(20);
        $stats = [
            'total' => SubscriptionPayment::sum('amount'),
            'pending' => SubscriptionPayment::pending()->sum('amount'),
            'completed' => SubscriptionPayment::completed()->sum('amount'),
        ];

        return view('admin.subscriptions.payments', compact('payments', 'stats'));
    }

    public function approvePayment(SubscriptionPayment $payment, Request $request = null)
    {
        try {
            if ($payment->status !== 'pending') {
                return redirect()->back()->with('error', 'Payment is not pending.');
            }

            // Get user and plan
            $user = $payment->user;
            $plan = SubscriptionPlan::find($payment->plan_id);
            
            if (!$user || !$plan) {
                return redirect()->back()->with('error', 'User or Plan not found. User: ' . ($user ? 'OK' : 'Missing') . ', Plan: ' . ($plan ? 'OK' : 'Missing'));
            }

            $payment->update([
                'status' => 'completed',
                'notes' => 'Approved by admin on ' . now()->format('Y-m-d H:i:s'),
            ]);

            // Calculate expiration
            $expiresAt = null;
            if ($plan->billing_period !== 'lifetime' && $plan->duration_days > 0) {
                $expiresAt = now()->addDays($plan->duration_days);
            }

            // Create subscription
            $subscription = ShopSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => $expiresAt,
                'auto_renew' => false,
            ]);

            $payment->update(['subscription_id' => $subscription->id]);

            return redirect()->back()->with('success', 'Payment approved and subscription activated for ' . $user->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving payment: ' . $e->getMessage());
        }
    }

    public function rejectPayment(SubscriptionPayment $payment, Request $request = null)
    {
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Payment is not pending.');
        }

        $payment->update([
            'status' => 'failed',
            'notes' => $request->notes ?? 'Rejected by admin on ' . now()->format('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Payment rejected.');
    }

    public function subscriptions(Request $request)
    {
        $query = ShopSubscription::with(['user', 'plan']);

        if ($request->status) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $subscriptions = $query->orderByDesc('created_at')->paginate(20);
        
        return view('admin.subscriptions.subscriptions', compact('subscriptions'));
    }

    public function cancelSubscription(ShopSubscription $subscription, Request $request = null)
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Subscription cancelled.');
    }

    public function stats()
    {
        $stats = [
            'total_plans' => SubscriptionPlan::count(),
            'active_plans' => SubscriptionPlan::where('status', true)->count(),
            'total_subscriptions' => ShopSubscription::count(),
            'active_subscriptions' => ShopSubscription::active()->count(),
            'total_revenue' => SubscriptionPayment::completed()->sum('amount'),
            'pending_payments' => SubscriptionPayment::pending()->count(),
            'revenue_this_month' => SubscriptionPayment::completed()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        $topPlans = ShopSubscription::selectRaw('plan_id, count(*) as count')
            ->groupBy('plan_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('plan')
            ->get();

        $recentPayments = SubscriptionPayment::with(['user', 'plan'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.subscriptions.stats', compact('stats', 'topPlans', 'recentPayments'));
    }

    public function commissionSettings()
    {
        $globalCommission = get_settings('shop_commission_rate') ?? 0;
        $useGlobalCommission = get_settings('shop_use_global_commission') ?? 0;
        
        return view('admin.subscriptions.commission_settings', compact('globalCommission', 'useGlobalCommission'));
    }

    public function updateCommissionSettings(Request $request)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        save_settings('shop_commission_rate', $request->commission_rate);
        save_settings('shop_use_global_commission', $request->has('use_global') ? 1 : 0);

        return redirect()->back()->with('success', 'Commission settings updated successfully.');
    }
}
