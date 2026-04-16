<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for starting out with limited features.',
                'price' => 0,
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'tier' => 'basic',
                'max_listings' => 3,
                'max_products' => 10,
                'has_analytics' => false,
                'has_custom_branding' => false,
                'has_priority_support' => false,
                'has_api_access' => false,
                'commission_rate' => 15,
                'is_featured' => false,
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'description' => 'Great value with more listings and products.',
                'price' => 49.99,
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'tier' => 'standard',
                'max_listings' => 10,
                'max_products' => 50,
                'has_analytics' => true,
                'has_custom_branding' => false,
                'has_priority_support' => false,
                'has_api_access' => false,
                'commission_rate' => 10,
                'is_featured' => true,
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Full access with all features enabled.',
                'price' => 99.99,
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'tier' => 'premium',
                'max_listings' => 0,
                'max_products' => 0,
                'has_analytics' => true,
                'has_custom_branding' => true,
                'has_priority_support' => true,
                'has_api_access' => true,
                'commission_rate' => 5,
                'is_featured' => true,
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Best for large businesses with custom needs.',
                'price' => 199.99,
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'tier' => 'enterprise',
                'max_listings' => 0,
                'max_products' => 0,
                'has_analytics' => true,
                'has_custom_branding' => true,
                'has_priority_support' => true,
                'has_api_access' => true,
                'commission_rate' => 2,
                'is_featured' => false,
                'status' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
