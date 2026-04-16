<?php

use App\Http\Controllers\Admin\Shop\ProductController;
use App\Http\Controllers\Admin\Shop\CategoryController;
use App\Http\Controllers\Admin\Shop\OrderController;
use App\Http\Controllers\Admin\Shop\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\Shop\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\Shop\InventoryController;
use App\Http\Controllers\Admin\Shop\InventoryCategoryController;
use App\Http\Controllers\Admin\Shop\DeliveryController;
use App\Http\Controllers\Admin\Shop\ApprovalController;
use App\Http\Controllers\Admin\Shop\StatisticsController;
use App\Http\Controllers\Agent\ShopApprovalController;
use App\Http\Controllers\OrderManager\DashboardController;
use App\Http\Controllers\Frontend\Shop\ShopController;
use App\Http\Controllers\Frontend\Shop\ReviewController;
use App\Http\Controllers\Frontend\Shop\WishlistController;
use App\Http\Controllers\Frontend\Shop\CouponController;
use App\Http\Controllers\Frontend\Shop\NotificationController;
use App\Http\Controllers\Frontend\Shop\VendorController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'anyAuth'])->group(function () {
    Route::get('shop/statistics', [StatisticsController::class, 'index'])->name('admin.shop.statistics');
    Route::get('shop/products', [ProductController::class, 'index'])->name('admin.shop.products');
    Route::get('shop/product/create', [ProductController::class, 'create'])->name('admin.shop.product.create');
    Route::post('shop/product/store', [ProductController::class, 'store'])->name('admin.shop.product.store');
    Route::get('shop/product/edit/{id}', [ProductController::class, 'edit'])->name('admin.shop.product.edit');
    Route::post('shop/product/update/{id}', [ProductController::class, 'update'])->name('admin.shop.product.update');
    Route::get('shop/product/delete/{id}', [ProductController::class, 'destroy'])->name('admin.shop.product.delete');
    Route::get('shop/product/image/delete/{id}', [ProductController::class, 'deleteImage'])->name('admin.shop.product.image.delete');

    Route::get('shop/categories', [CategoryController::class, 'index'])->name('admin.shop.categories');
    Route::post('shop/category/store', [CategoryController::class, 'store'])->name('admin.shop.category.store');
    Route::post('shop/category/update/{id}', [CategoryController::class, 'update'])->name('admin.shop.category.update');
    Route::get('shop/category/delete/{id}', [CategoryController::class, 'destroy'])->name('admin.shop.category.delete');

    Route::get('shop/orders', [OrderController::class, 'index'])->name('admin.shop.orders');
    Route::get('shop/order/{id}', [OrderController::class, 'show'])->name('admin.shop.order.show');
    Route::post('shop/order/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.shop.order.status');
    Route::post('shop/order/{id}/payment', [OrderController::class, 'updatePaymentStatus'])->name('admin.shop.order.payment');
    Route::post('shop/order/{id}/tracking', [OrderController::class, 'updateTracking'])->name('admin.shop.order.tracking');
    Route::get('shop/order/{id}/delete', [OrderController::class, 'destroy'])->name('admin.shop.order.delete');

    Route::get('shop/reviews', [AdminReviewController::class, 'index'])->name('admin.shop.reviews');
    Route::post('shop/review/{id}/status', [AdminReviewController::class, 'updateStatus'])->name('admin.shop.review.status');
    Route::get('shop/review/{id}/delete', [AdminReviewController::class, 'destroy'])->name('admin.shop.review.delete');

    Route::get('shop/coupons', [AdminCouponController::class, 'index'])->name('admin.shop.coupons');
    Route::post('shop/coupon/store', [AdminCouponController::class, 'store'])->name('admin.shop.coupon.store');
    Route::post('shop/coupon/{id}/update', [AdminCouponController::class, 'update'])->name('admin.shop.coupon.update');
    Route::get('shop/coupon/{id}/delete', [AdminCouponController::class, 'destroy'])->name('admin.shop.coupon.delete');
    Route::get('shop/coupon/generate-code', [AdminCouponController::class, 'generateCode'])->name('admin.shop.coupon.generate');

    Route::get('shop/inventory', [InventoryController::class, 'index'])->name('admin.shop.inventory');
    Route::get('shop/inventory/create', [InventoryController::class, 'create'])->name('admin.shop.inventory.create');
    Route::post('shop/inventory/store', [InventoryController::class, 'store'])->name('admin.shop.inventory.store');
    Route::get('shop/inventory/edit/{id}', [InventoryController::class, 'edit'])->name('admin.shop.inventory.edit');
    Route::post('shop/inventory/update/{id}', [InventoryController::class, 'update'])->name('admin.shop.inventory.update');
    Route::get('shop/inventory/delete/{id}', [InventoryController::class, 'destroy'])->name('admin.shop.inventory.delete');

    Route::get('shop/inventory/categories', [InventoryCategoryController::class, 'index'])->name('admin.shop.inventory.categories');
    Route::post('shop/inventory/category/store', [InventoryCategoryController::class, 'store'])->name('admin.shop.inventory.category.store');
    Route::post('shop/inventory/category/update/{id}', [InventoryCategoryController::class, 'update'])->name('admin.shop.inventory.category.update');
    Route::get('shop/inventory/category/delete/{id}', [InventoryCategoryController::class, 'destroy'])->name('admin.shop.inventory.category.delete');

    Route::get('shop/approval', [ApprovalController::class, 'index'])->name('admin.shop.approval');
    Route::get('shop/approval/{id}', [ApprovalController::class, 'show'])->name('admin.shop.approval.show');
    Route::post('shop/approval/{id}/approve', [ApprovalController::class, 'approve'])->name('admin.shop.approval.approve');
    Route::post('shop/approval/{id}/reject', [ApprovalController::class, 'reject'])->name('admin.shop.approval.reject');

    Route::get('shop/delivery', [DeliveryController::class, 'index'])->name('admin.shop.delivery');
    Route::get('shop/delivery/settings', [DeliveryController::class, 'settings'])->name('admin.shop.delivery.settings');
    Route::post('shop/delivery/settings/update', [DeliveryController::class, 'updateSettings'])->name('admin.shop.delivery.settings.update');
    Route::get('shop/delivery/{id}', [DeliveryController::class, 'show'])->name('admin.shop.delivery.show');
    Route::post('shop/delivery/{id}/update-status', [DeliveryController::class, 'updateDeliveryStatus'])->name('admin.shop.delivery.update-status');
});

Route::prefix('agent')->middleware(['auth', 'anyAuth', 'shopSubscription'])->group(function () {
    Route::get('shop/products', [ProductController::class, 'index'])->name('agent.shop.products');
    Route::get('shop/product/create', [ProductController::class, 'create'])->name('agent.shop.product.create');
    Route::post('shop/product/store', [ProductController::class, 'store'])->name('agent.shop.product.store');
    Route::get('shop/product/edit/{id}', [ProductController::class, 'edit'])->name('agent.shop.product.edit');
    Route::post('shop/product/update/{id}', [ProductController::class, 'update'])->name('agent.shop.product.update');
    Route::get('shop/product/delete/{id}', [ProductController::class, 'destroy'])->name('agent.shop.product.delete');

    Route::get('shop/orders', [OrderController::class, 'index'])->name('agent.shop.orders');
    Route::get('shop/order/{id}', [OrderController::class, 'show'])->name('agent.shop.order.show');
    Route::post('shop/order/{id}/status', [OrderController::class, 'updateStatus'])->name('agent.shop.order.status');
    Route::post('shop/order/{id}/payment', [OrderController::class, 'updatePaymentStatus'])->name('agent.shop.order.payment');

    Route::get('shop/reviews', [ReviewController::class, 'index'])->name('agent.shop.reviews');

    Route::get('shop/approval', [ShopApprovalController::class, 'index'])->name('agent.shop.approval');
    Route::get('shop/approval/{id}', [ShopApprovalController::class, 'show'])->name('agent.shop.approval.show');
    Route::post('shop/approval/{id}/approve', [ShopApprovalController::class, 'approve'])->name('agent.shop.approval.approve');
    Route::post('shop/approval/{id}/reject', [ShopApprovalController::class, 'reject'])->name('agent.shop.approval.reject');
});

Route::prefix('order-manager')->middleware(['auth', 'anyAuth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('order.manager.dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('order.manager.analytics');
    
    Route::get('/approval', [DashboardController::class, 'approvalOrders'])->name('order.manager.approval');
    Route::get('/approval/{id}', [DashboardController::class, 'approvalShow'])->name('order.manager.approval.show');
    Route::post('/approval/{id}/approve', [DashboardController::class, 'approveOrder'])->name('order.manager.approval.approve');
    Route::post('/approval/{id}/reject', [DashboardController::class, 'rejectOrder'])->name('order.manager.approval.reject');
    
    Route::get('/delivery', [DashboardController::class, 'deliveryOrders'])->name('order.manager.delivery');
    Route::get('/delivery/settings', [DashboardController::class, 'deliverySettings'])->name('order.manager.delivery.settings');
    Route::post('/delivery/settings/update', [DashboardController::class, 'updateDeliverySettings'])->name('order.manager.delivery.settings.update');
    Route::get('/delivery/{id}', [DashboardController::class, 'deliveryShow'])->name('order.manager.delivery.show');
    Route::post('/delivery/{id}/update-status', [DashboardController::class, 'updateDeliveryStatus'])->name('order.manager.delivery.update-status');
    
    Route::get('/orders', [DashboardController::class, 'allOrders'])->name('order.manager.orders');
    Route::get('/orders/{id}', [DashboardController::class, 'orderShow'])->name('order.manager.order.show');
});

Route::middleware(['web'])->group(function () {
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::get('/shop/vendors', [VendorController::class, 'index'])->name('shop.vendors');
    Route::get('/shop/vendor/{id}', [VendorController::class, 'show'])->name('shop.vendor');
    Route::get('/shop/vendor/{id}/products', [VendorController::class, 'products'])->name('shop.vendor.products');
    Route::get('/shop/vendor/{id}/reviews', [VendorController::class, 'reviews'])->name('shop.vendor.reviews');
    Route::get('/shop/track', [ShopController::class, 'trackOrder'])->name('shop.track');
    Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.product')
        ->where('slug', '(?!cart|checkout|orders|wishlist|my-reviews|track|vendors)[a-z0-9-]+');
});

Route::middleware(['auth', 'anyAuth'])->group(function () {
    Route::post('/shop/cart/add', [ShopController::class, 'addToCart'])->name('shop.cart.add');
    Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
    Route::post('/shop/cart/update', [ShopController::class, 'updateCart'])->name('shop.cart.update');
    Route::get('/shop/cart/remove/{id}', [ShopController::class, 'removeFromCart'])->name('shop.cart.remove');
    Route::post('/shop/coupon/validate', [CouponController::class, 'validateCoupon'])->name('shop.coupon.validate');
    Route::get('/shop/checkout', [ShopController::class, 'showCheckout'])->name('shop.checkout');
    Route::post('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout.process');
    Route::get('/shop/orders', [ShopController::class, 'orders'])->name('shop.orders');
    Route::get('/shop/order/{id}', [ShopController::class, 'orderDetail'])->name('shop.order');
    Route::get('/shop/order/{id}/invoice', [ShopController::class, 'invoice'])->name('shop.order.invoice');
    Route::get('/shop/order/{id}/invoice/download', [ShopController::class, 'downloadInvoice'])->name('shop.order.invoice.download');

    Route::post('/shop/review/store', [ReviewController::class, 'store'])->name('shop.review.store');
    Route::get('/shop/my-reviews', [ReviewController::class, 'myReviews'])->name('shop.my-reviews');

    Route::post('/shop/wishlist/toggle', [WishlistController::class, 'toggle'])->name('shop.wishlist.toggle');
    Route::get('/shop/wishlist', [WishlistController::class, 'index'])->name('shop.wishlist');
    Route::get('/shop/wishlist/count', [WishlistController::class, 'count'])->name('shop.wishlist.count');
    
    Route::get('/shop/notifications', [NotificationController::class, 'index'])->name('shop.notifications');
    Route::get('/shop/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('shop.notifications.read');
    Route::get('/shop/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('shop.notifications.mark-all-read');
});
