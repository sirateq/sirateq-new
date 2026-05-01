<?php

namespace App\Http\Controllers;

class MainController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function about()
    {
        return view('about');
    }

    public function services()
    {
        return view('services');
    }

    public function serviceShow($slug)
    {
        $title = ucwords(str_replace('-', ' ', $slug));

        return view('services.show', [
            'slug' => $slug,
        ]);
    }

    public function ourWorks()
    {
        return view('portfolio');
    }

    public function ourWorkShow($slug)
    {
        $title = ucwords(str_replace('-', ' ', $slug));

        return view('portfolio.show', [
            'slug' => $slug,
        ]);
    }

    public function products()
    {
        return view('products');
    }

    public function productShow($slug)
    {
        $title = ucwords(str_replace('-', ' ', $slug));

        // Custom override for Pollvite
        $seoTitle = $slug === 'pollvite'
            ? 'Pollvite - Empower Your Events with Endless Possibilities'
            : $title . ' - Sirateq';

        $seoDescription = $slug === 'pollvite'
            ? 'Pollvite is an event ticketing and e-voting solution for seamless organization.'
            : 'Learn more about our ' . $title . ' product.';

        return view('portfolio.project-show', [
            'slug' => $slug,
        ]);
    }

    public function contact()
    {
        return view('contact');
    }
}
