<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

// Static Pages
Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('/about-us', [MainController::class, 'about'])->name('about-us');
Route::get('/contact-us', [MainController::class, 'contact'])->name('contact-us');

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

// Route::get('test', function () {
//     return new \App\Mail\ContactUserConfirmation([
//         'first_name' => 'John',
//         'last_name' => 'Doe',
//         'company' => 'Sirateq Ghana Group LTD',
//         'email' => fake()->email(),
//         'phone' => '0241234567',
//         'service' => 'Web Development',
//         'message' => 'I need a website for my business',
//     ]);
// });
