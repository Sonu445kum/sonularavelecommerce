<footer class="bg-gray-900 text-gray-300 mt-10 py-10">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 px-6">
        <div>
            <h5 class="text-white font-bold mb-3 text-lg">MyShop</h5>
            <p class="text-gray-400 text-sm">Buy your favorite products online at best prices. Fast delivery and secure payments.</p>
        </div>

        <div>
            <h6 class="text-white font-semibold mb-2">Quick Links</h6>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ url('/') }}" class="hover:text-white">Home</a></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-white">Products</a></li>
                <li><a href="{{ route('cart.index') }}" class="hover:text-white">Cart</a></li>
                <li><a href="{{ route('checkout.index') }}" class="hover:text-white">Checkout</a></li>
            </ul>
        </div>

        <div>
            <h6 class="text-white font-semibold mb-2">Support</h6>
            <ul class="space-y-2 text-sm">
                <li><a href="#" class="hover:text-white">Contact Us</a></li>
                <li><a href="#" class="hover:text-white">FAQs</a></li>
                <li><a href="#" class="hover:text-white">Terms & Conditions</a></li>
                <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
            </ul>
        </div>

        <div>
            <h6 class="text-white font-semibold mb-2">Follow Us</h6>
            <div class="flex space-x-4 text-xl">
                <a href="#" class="hover:text-blue-400"><i class="bi bi-facebook"></i></a>
                <a href="#" class="hover:text-pink-400"><i class="bi bi-instagram"></i></a>
                <a href="#" class="hover:text-sky-400"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="hover:text-blue-600"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-700 mt-8 pt-4 text-center text-gray-500 text-sm">
        © {{ date('Y') }} MyShop. All rights reserved.
    </div>
</footer>
