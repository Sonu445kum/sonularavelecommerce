{{-- =============================================
    partials/footer.blade.php
    → Clean responsive footer for all pages
============================================= --}}
<footer class="bg-gray-900 text-gray-300 mt-5 pt-5 pb-3">
    <div class="container">
        <div class="row gy-3">
            <div class="col-md-3">
                <h5 class="text-white fw-bold mb-3">MyShop</h5>
                <p>Buy your favorite products online at best prices. Fast delivery and secure payments.</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white fw-semibold mb-2">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white text-decoration-none">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-400 hover:text-white text-decoration-none">Products</a></li>
                    <li><a href="{{ route('cart.index') }}" class="text-gray-400 hover:text-white text-decoration-none">Cart</a></li>
                    <li><a href="{{ route('checkout.index') }}" class="text-gray-400 hover:text-white text-decoration-none">Checkout</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white fw-semibold mb-2">Support</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-gray-400 hover:text-white text-decoration-none">Contact Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-decoration-none">FAQs</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-decoration-none">Terms & Conditions</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-decoration-none">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white fw-semibold mb-2">Follow Us</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <hr class="border-gray-700 my-4">
        <div class="text-center text-gray-400 small">
            © {{ date('Y') }} MyShop. All rights reserved.
        </div>
    </div>
</footer>
