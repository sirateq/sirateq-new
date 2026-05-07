<?php

use App\Http\Controllers\Admin\OrderInvoiceController as AdminOrderInvoiceController;
use App\Http\Controllers\Admin\OrdersExportController;
use App\Http\Controllers\Admin\ProductsExportController;
use App\Http\Controllers\Admin\ProductsImportController;
use App\Http\Controllers\Admin\ProductsImportTemplateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Shop\OrderInvoiceController;
use App\Http\Controllers\Shop\SignedShopOrderController;
use App\Livewire\Admin\Categories\Form as CategoryForm;
use App\Livewire\Admin\Categories\Index as CategoryIndex;
use App\Livewire\Admin\Categories\Show as CategoryShow;
use App\Livewire\Admin\Categories\Trash as CategoryTrash;
use App\Livewire\Admin\Customers\Index as CustomersIndex;
use App\Livewire\Admin\Customers\Show as CustomersShow;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Discounts\Index as DiscountIndex;
use App\Livewire\Admin\Inventory\Index as InventoryIndex;
use App\Livewire\Admin\Orders\Index as OrderIndex;
use App\Livewire\Admin\Orders\Show as OrderShow;
use App\Livewire\Admin\Products\Form as ProductForm;
use App\Livewire\Admin\Products\Index as ProductIndex;
use App\Livewire\Admin\Users\Index as AdminUsersIndex;
use App\Livewire\Shop\CartPage;
use App\Livewire\Shop\Catalog;
use App\Livewire\Shop\CheckoutPage;
use App\Livewire\Shop\OrderConfirmation;
use App\Livewire\Shop\OrderTracking;
use App\Livewire\Shop\ProductShow;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', AdminDashboard::class)->name('dashboard');
});

require __DIR__.'/settings.php';

// Static Pages
Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('/about-us', [MainController::class, 'about'])->name('about-us');
Route::get('/contact-us', [MainController::class, 'contact'])->name('contact-us');
Route::get('/contact', fn () => to_route('contact-us'))->name('contact');

// Services / Solutions
Route::get('/services', [MainController::class, 'services'])->name('services');
Route::get('/services/{slug}', [MainController::class, 'serviceShow'])->name('services.show');

// Portfolio / Our Works
Route::get('/our-works', [MainController::class, 'ourWorks'])->name('our-works');
Route::get('/our-works/{slug}', [MainController::class, 'ourWorkShow'])->name('our-works.show');

// Projects / Products
Route::get('/products', [MainController::class, 'products'])->name('products');
Route::get('/products/{slug}', [MainController::class, 'productShow'])->name('products.show');

// Form Actions
Route::post('/contact', [ContactController::class, 'submit'])->name('contact');

Route::prefix('shop')->group(function () {
    Route::livewire('/', Catalog::class)->name('shop.index');
    Route::livewire('/products/{product:slug}', ProductShow::class)->name('shop.products.show');
    Route::livewire('/track', OrderTracking::class)->name('shop.orders.track');
    Route::livewire('/cart', CartPage::class)->name('shop.cart');
    Route::livewire('/checkout', CheckoutPage::class)->name('shop.checkout');
    Route::get('/orders/{order}/signed', [SignedShopOrderController::class, 'grantAndRedirectToOrder'])
        ->middleware('signed')
        ->name('shop.orders.signed-show');
    Route::get('/orders/{order}/invoice/signed', [SignedShopOrderController::class, 'grantAndRedirectToInvoice'])
        ->middleware('signed')
        ->name('shop.orders.signed-invoice');
    Route::get('/orders/{order}/invoice', OrderInvoiceController::class)->name('shop.orders.invoice');
    Route::livewire('/orders/{order}', OrderConfirmation::class)->name('shop.orders.show');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/exports/orders', OrdersExportController::class)->name('admin.exports.orders');
    Route::get('/exports/products', ProductsExportController::class)->name('admin.exports.products');
    Route::get('/imports/products/template', ProductsImportTemplateController::class)->name('admin.products.import.template');
    Route::post('/imports/products', ProductsImportController::class)->name('admin.products.import');
    Route::livewire('/products', ProductIndex::class)->name('admin.products.index');
    Route::livewire('/products/create', ProductForm::class)->name('admin.products.create');
    Route::livewire('/products/{product}/edit', ProductForm::class)->name('admin.products.edit');
    Route::livewire('/categories', CategoryIndex::class)->name('admin.categories.index');
    Route::livewire('/categories/trash', CategoryTrash::class)->name('admin.categories.trash');
    Route::livewire('/categories/create', CategoryForm::class)->name('admin.categories.create');
    Route::livewire('/categories/{categoryId}/view', CategoryShow::class)->name('admin.categories.show');
    Route::livewire('/categories/{category}/edit', CategoryForm::class)->name('admin.categories.edit');
    Route::livewire('/orders', OrderIndex::class)->name('admin.orders.index');
    Route::get('/orders/{order}/invoice', AdminOrderInvoiceController::class)->name('admin.orders.invoice');
    Route::livewire('/orders/{order}', OrderShow::class)->name('admin.orders.show');
    Route::livewire('/inventory', InventoryIndex::class)->name('admin.inventory.index');
    Route::livewire('/discounts', DiscountIndex::class)->name('admin.discounts.index');
    Route::livewire('/users', AdminUsersIndex::class)->name('admin.users.index');
    Route::livewire('/customers', CustomersIndex::class)->name('admin.customers.index');
    Route::livewire('/customers/{user}', CustomersShow::class)->name('admin.customers.show');
});

Route::get('test', function () {

    // get user and change role to admin
    $user = User::first();
    $user->is_admin = true;
    $user->save();

    return $user;
});
