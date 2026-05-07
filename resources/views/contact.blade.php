<x-layouts.app title="Contact">
    <x-breadcrum title="Contact Us" />

    <!--==============================
        Contact Section
        ==============================-->
    <section class="tv-contact-section inner space bg-light">
        <div class="container">
            <div class="row gy-30">
                <div class="col-lg-5">
                    <div class="contact-content-wrap">
                        <!-- Section Title -->
                        <div class="title-wrap" data-wow-duration="1.5s" data-wow-delay=".4s">
                            <div class="sub-title-2 text-theme"><i class="fa-solid fa-circle-check"></i>Contact Us</div>
                            <h2 class="sec-title">Get in Touch with Sirateq Ghana</h2>
                            <p>We are ready to support your business with reliable digital solutions.</p>
                        </div>
                        <div class="contact-info">
                            <div class="contact-item">
                                <div class="icon">
                                    <i class="fa-sharp fa-regular fa-location-dot"></i>
                                </div>
                                <div class="info">
                                    <h4 class="title">Address</h4>
                                    <p>Alhaji Junction, Greater Accra, Ghana</p>
                                    <a href="https://maps.google.com/?q=Alhaji+Junction,+Greater+Accra,+Ghana"
                                        target="_blank" rel="noopener noreferrer">
                                        <span class="link-effect">
                                            <span class="effect-1">Get direction</span>
                                            <span class="effect-1">Get direction</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="icon">
                                    <i class="fa-light fa-envelope"></i>
                                </div>
                                <div class="info">
                                    <h4 class="title">Email Address</h4>
                                    <div class="content">
                                        <a href="mailto:info@sirateqghana.com">info@sirateqghana.com</a>
                                    </div>
                                    <a href="mailto:info@sirateqghana.com">
                                        <span class="link-effect">
                                            <span class="effect-1">Send message</span>
                                            <span class="effect-1">Send message</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="icon">
                                    <i class="fa-light fa-circle-phone"></i>
                                </div>
                                <div class="info">
                                    <h4 class="title">Phone Number</h4>
                                    <div class="content">
                                        <a href="tel:+233362296798">+233 36 229 6798</a>
                                    </div>
                                    <a href="tel:+233362296798">
                                        <span class="link-effect">
                                            <span class="effect-1">Call anytime</span>
                                            <span class="effect-1">Call anytime</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="social-links">
                            <a href="https://www.facebook.com/sirateq" target="_blank" rel="noopener noreferrer">
                                <span class="link-effect">
                                    <span class="effect-1">Facebook</span>
                                    <span class="effect-1">Facebook</span>
                                </span>
                            </a>
                            <a href="https://x.com/sirateq_ghana" target="_blank" rel="noopener noreferrer">
                                <span class="link-effect">
                                    <span class="effect-1">Twitter/X</span>
                                    <span class="effect-1">Twitter/X</span>
                                </span>
                            </a>
                            <a href="https://www.linkedin.com/company/sirateq_ghana" target="_blank"
                                rel="noopener noreferrer">
                                <span class="link-effect">
                                    <span class="effect-1">LinkedIn</span>
                                    <span class="effect-1">LinkedIn</span>
                                </span>
                            </a>
                            <a href="https://www.instagram.com/sirateq_ghana/" target="_blank"
                                rel="noopener noreferrer">
                                <span class="link-effect">
                                    <span class="effect-1">Instagram</span>
                                    <span class="effect-1">Instagram</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="contact-form">
                        <h2 class="title mt--5 mb-35">Let’s Contact with us</h2>
                        @if (session('success'))
                            <div style="margin-bottom:20px;padding:14px 16px;border-radius:10px;background:#e8f8ee;border:1px solid #b7e4c7;color:#1f7a3e;font-weight:500;">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div style="margin-bottom:20px;padding:14px 16px;border-radius:10px;background:#fdecec;border:1px solid #f5c2c7;color:#b4232d;font-weight:500;">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form class="contact-form-native" action="{{ route('contact-us.submit') }}" method="post">
                            @csrf
                            <div class="form-grid">
                                <div class="form-group">
                                    <input type="text" id="firstName" name="first_name" placeholder="First Name"
                                        required autocomplete="given-name" value="{{ old('first_name') }}">
                                </div>
                                <div class="form-group">
                                    <input type="text" id="lastName" name="last_name" placeholder="Last Name"
                                        required autocomplete="family-name" value="{{ old('last_name') }}">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <input type="email" id="userEmail" name="email" placeholder="Email Address"
                                        required autocomplete="email" value="{{ old('email') }}">
                                </div>
                                <div class="form-group">
                                    <input type="text" id="company" name="company"
                                        placeholder="Company (Optional)" autocomplete="organization"
                                        value="{{ old('company') }}">
                                </div>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <input type="text" id="phone" name="phone" placeholder="Phone No."
                                        required autocomplete="tel" value="{{ old('phone') }}">
                                </div>
                                <div class="form-group">
                                    <select class="custom-select" id="service" name="service" required>
                                        <option value="" disabled {{ old('service') ? '' : 'selected' }}>What do
                                            you need?</option>
                                        <option value="Web Development & Design"
                                            {{ old('service') === 'Web Development & Design' ? 'selected' : '' }}>Web
                                            Development & Design</option>
                                        <option value="Mobile Application Development"
                                            {{ old('service') === 'Mobile Application Development' ? 'selected' : '' }}>
                                            Mobile Application Development</option>
                                        <option value="IT Consultation & Advisory"
                                            {{ old('service') === 'IT Consultation & Advisory' ? 'selected' : '' }}>IT
                                            Consultation & Advisory</option>
                                        <option value="Cloud Services"
                                            {{ old('service') === 'Cloud Services' ? 'selected' : '' }}>Cloud Services
                                        </option>
                                        <option value="Data Intelligence & IoT Solutions"
                                            {{ old('service') === 'Data Intelligence & IoT Solutions' ? 'selected' : '' }}>
                                            Data Intelligence & IoT Solutions</option>
                                        <option value="Specialized Technology Solutions"
                                            {{ old('service') === 'Specialized Technology Solutions' ? 'selected' : '' }}>
                                            Specialized Technology Solutions</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea id="message" name="message" placeholder="Write Message" required>{{ old('message') }}</textarea>
                            </div> 
                            <button type="submit" class="theme-btn  mt-30" data-loading-text="Please wait...">
                                <span class="link-effect">
                                    <span class="effect-1">Submit Now</span>
                                    <span class="effect-1">Submit Now</span>
                                </span>
                                <span class="arrow-all">
                                    <i>
                                        <svg width="16" height="19" viewBox="0 0 12 12" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 6H10M10 6L6 2M10 6L6 10" stroke="#1053f3" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <svg width="16" height="19" viewBox="0 0 12 12" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 6H10M10 6L6 2M10 6L6 10" stroke="#1053f3" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </i>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
