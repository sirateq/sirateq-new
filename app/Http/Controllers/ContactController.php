<?php

namespace App\Http\Controllers;

use App\Mail\ContactAdminAlert;
use App\Mail\ContactUserConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $formFields = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'service' => 'required|string|max:255',
            'message' => 'required|string',
            // 'g-recaptcha-response' => 'required|recaptchav3:contact,0.5'
            'g-recaptcha-response' => 'required|recaptchav3:contact,0.5'
        ], [
            'g-recaptcha-response.required' => 'Human verification failed.',
            'g-recaptcha-response.recaptchav3' => 'Human verification failed.',
        ]);

        // Send email to Sirateq admins synchronously
        try {
            Mail::to([
                'issakuhafiz.ih@gmail.com',
                'bundanaabdulhafiz@gmail.com',
                'sirateqghana@gmail.com',
                'info@sirateqghana.com'
            ])->sendNow(new ContactAdminAlert($formFields));


            // Send confirmation email to the user synchronously
            // defer(function () use ($formFields) {
            Mail::to($formFields['email'])
                ->send(new ContactUserConfirmation($formFields));
            // });

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while sending your message. Please try again later.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Your message has been sent successfully.',
            ]);
        }

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
