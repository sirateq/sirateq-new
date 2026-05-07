<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Product;
use App\Policies\CategoryPolicy;
use App\Policies\CouponPolicy;
use App\Policies\InventoryItemPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(InventoryItem::class, InventoryItemPolicy::class);
        Gate::policy(Coupon::class, CouponPolicy::class);
    }
}
