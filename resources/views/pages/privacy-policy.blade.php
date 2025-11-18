@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">

    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800">
            Privacy <span class="text-blue-600">Policy</span>
        </h1>
        <p class="text-gray-600 text-lg mt-2">
            We value your privacy and are committed to protecting your personal information.
        </p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-md space-y-10 text-gray-700">

        <div>
            <h2 class="text-2xl font-semibold mb-2">1. Information We Collect</h2>
            <p>
                We collect personal information such as your name, email, phone number, shipping address, 
                and payment details when you place an order or create an account. We also collect usage data 
                such as IP address, browser type, and device information for security and analytics purposes.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">2. How We Use Your Information</h2>
            <ul class="list-disc pl-6 space-y-2">
                <li>To process and deliver your orders</li>
                <li>To improve our website and customer experience</li>
                <li>To provide order updates and notifications</li>
                <li>To enhance security and detect fraudulent activities</li>
                <li>To offer personalized recommendations and promotions</li>
            </ul>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">3. Sharing Your Information</h2>
            <p>
                We do <strong>not</strong> sell or rent your personal data.  
                We may share it only with:
            </p>
            <ul class="list-disc pl-6 space-y-2 mt-2">
                <li>Trusted delivery partners for shipping your order</li>
                <li>Payment gateways to verify and process payments</li>
                <li>Service providers who help us operate our website</li>
                <li>Legal authorities, only if required by law</li>
            </ul>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">4. Data Security</h2>
            <p>
                We use industry-standard encryption, secure servers, and strict access controls to protect your data. 
                However, no online platform can guarantee 100% security, but we follow best practices to keep your 
                information safe.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">5. Cookies</h2>
            <p>
                Our website uses cookies to enhance your browsing experience. You can choose to disable cookies 
                in your browser settings, but some features may not work properly without them.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">6. Third-Party Links</h2>
            <p>
                Our site may contain links to third-party websites. We are not responsible for the content or 
                privacy practices of those websites.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">7. Your Rights</h2>
            <p>You have the right to:</p>
            <ul class="list-disc pl-6 space-y-2 mt-2">
                <li>Access, update, or delete your personal information</li>
                <li>Request a copy of the data we store about you</li>
                <li>Opt out of marketing communications</li>
            </ul>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-2">8. Updates to This Policy</h2>
            <p>
                We may update this Privacy Policy from time to time. Any changes will be updated on this page 
                with a revised “Last Updated” date.
            </p>
        </div>

        <div class="pt-4">
            <p class="text-gray-600 text-sm">
                <strong>Last Updated:</strong> {{ now()->format('F d, Y') }}
            </p>
        </div>

    </div>

</div>
@endsection
