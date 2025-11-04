<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class PageController extends Controller
{
    /**
     * 📄 About Page
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * 💌 Show Contact Page
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * 📬 Handle Contact Form Submission
     */
    public function sendContact(Request $request)
    {
        // ✅ Validate user input
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // 📨 Send email to admin or support
            Mail::to('sonuadmin445k@yopmail.com')->send(new ContactMail([
                'name'    => $request->name,
                'email'   => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]));

            // ✅ Redirect with success message
            return back()->with('success', 'Your message has been sent successfully!');
        } catch (\Exception $e) {
            \Log::error('❌ Contact form email failed: ' . $e->getMessage());
            return back()->withErrors('There was an error sending your message. Please try again later.');
        }
    }
}
