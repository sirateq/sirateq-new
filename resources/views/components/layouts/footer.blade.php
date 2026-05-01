<footer class="footer-section position-relative z-1 mx-30 mb-30 overflow-hidden bg-dark xxl-m-0">
    <div class="bg image" style="opacity:.18;">
        <img src="assets/images/footer/hm1-bg01.webp" alt="Footer background">
    </div>
    <div class="container py-60 py-md-45 py-sm-35">
        <div class="row align-items-start g-4">
            <div class="col-lg-7">
                <h2 class="mb-0 text-white" style="font-size: clamp(34px, 5.2vw, 64px); line-height: .98; font-weight: 700; letter-spacing: -.02em;">
                    Let's Work<br>Together
                </h2>
            </div>
            <div class="col-lg-5">
                <p class="mb-20 text-white" style="font-size: 16px; line-height: 1.7; max-width: 620px; opacity:.92;">
                    Partner with us to transform your business through smart digital solutions, reliable IT services, and advanced technology systems designed for sustainable growth.
                </p>
                <a href="{{ route('contact-us') }}" class="theme-btn br-30">
                    <span class="link-effect">
                        <span class="effect-1">LET'S TALK WITH US</span>
                        <span class="effect-1">LET'S TALK WITH US</span>
                    </span>
                </a>
            </div>
        </div>

        <div class="mt-45 mt-md-35 mt-sm-30 border-top border-white" style="opacity:.2;"></div>

        <div class="py-18">
            <ul class="list-inline mb-0 d-flex flex-wrap gap-3" style="font-size: 13px; font-weight: 700; letter-spacing: .03em;">
                <li class="list-inline-item"><a href="{{ route('about-us') }}" class="text-white">ABOUT COMPANY</a></li>
                <li class="list-inline-item"><a href="{{ route('services') }}" class="text-white">SOLUTIONS</a></li>
                <li class="list-inline-item"><a href="{{ route('products') }}" class="text-white">PRODUCTS</a></li>
                <li class="list-inline-item"><a href="{{ route('contact-us') }}" class="text-white">CONTACT US</a></li>
            </ul>
        </div>

        <div class="border-top border-white" style="opacity:.2;"></div>

        <div class="row align-items-center gy-2 py-18">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3" style="font-size: 16px;">
                    <a href="https://www.facebook.com/sirateq" target="_blank" rel="noopener noreferrer" class="text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/sirateq_ghana/" target="_blank" rel="noopener noreferrer" class="text-white"><i class="fab fa-instagram"></i></a>
                    <a href="https://x.com/sirateq_ghana" target="_blank" rel="noopener noreferrer" class="text-white"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 text-white" style="font-size: 15px; opacity:.92;">
                    Copyright &copy; {{ now()->year }} Sirateq Ghana Group LTD
                </p>
            </div>
        </div>
    </div>
</footer>
