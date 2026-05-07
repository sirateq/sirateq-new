<header>
    <!--=============================
        Header Area
        ==============================-->
    <header class="tv-header header-style6">
        <div class="main-wrapper">
            <!-- Main Menu Area -->
            <div class="menu-area">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto logo">
                        <div class="header-logo">
                            <a href="{{ route('home') }}">
                                <img alt="logo" src="{{ asset('logo.png') }}" width="100" height="100">
                                <img alt="logo" src="{{ asset('logo.png') }}" width="100" height="100">
                            </a>
                        </div>
                    </div>
                    <div class="col-auto nav-outer">
                        <div class="nav-menu">
                            <nav class="main-menu d-none d-lg-inline-block">
                                <ul class="navigation">
                                    <li class="active">
                                        <a class="active" href="{{ route('home') }}">Home</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('about-us') }}">About us</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('services') }}">Services</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('products') }}">Our Products</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('contact-us') }}">Contact</a>
                                    </li>
                                </ul>
                            </nav>
                            <div class="navbar-right d-inline-flex d-lg-none">
                                <button class="menu-toggle sidebar-btn" type="button">
                                    <span class="line"></span>
                                    <span class="line"></span>
                                    <span class="line"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto header-right-wrapper">
                        <div class="outer-box">
                            <!-- <button class="search-btn">
                                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                </button> -->
                            <a href="{{ route('contact-us') }}" class="theme-btn">
                                <span class="link-effect">
                                    <span class="effect-1">Contact Us</span>
                                    <span class="effect-1">Contact Us</span>
                                </span>
                                <span class="arrow-all">
                                    <i>
                                        <svg width="16" height="19" viewBox="0 0 12 12" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 6H10M10 6L6 2M10 6L6 10" stroke="#061153" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <svg width="16" height="19" viewBox="0 0 12 12" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 6H10M10 6L6 2M10 6L6 10" stroke="#061153" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </i>
                                </span>
                            </a>

                            @auth
                                <flux:button href="{{ route('dashboard') }}" icon:trailing="arrow-up-right">
                                    Dashboard
                                </flux:button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!--==============================
        Mobile Menu
        ============================== -->
    <div class="mobile-menu-wrapper">
        <div class="mobile-menu-area">
            <button class="menu-toggle"><i class="fas fa-times"></i></button>
            <div class="mobile-logo">
                <a href="{{ route('home') }}"><img alt="Pureflow" src="{{ asset('logo.png') }}" width="100"
                        height="100"></a>
            </div>
            <div class="mobile-menu">
                <ul class="navigation clearfix">
                    <!--Keep This Empty / Menu will come through Javascript-->
                </ul>
            </div>

        </div>
    </div>

    <!--==============================
        Sticky Header
        ============================== -->
    <div class="sticky-header">
        <div class="container">
            <!-- Main Menu Area -->
            <div class="menu-area">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto logo">
                        <div class="header-logo">
                            <a href="{{ route('home') }}">
                                <img alt="logo" src="{{ asset('logo.png') }}" width="100" height="100">
                                <img alt="logo" src="{{ asset('logo.png') }}" width="100" height="100">
                            </a>
                        </div>
                    </div>
                    <div class="col-auto nav-menu">
                        <nav class="main-menu d-none d-lg-inline-block">
                            <ul class="navigation clearfix">
                                <!--Keep This Empty / Menu will come through Javascript-->
                            </ul>
                        </nav>
                        <div class="navbar-right d-inline-flex d-lg-none">
                            <button class="menu-toggle sidebar-btn" type="button">
                                <span class="line"></span>
                                <span class="line"></span>
                                <span class="line"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Header Area -->

    <!-- Header Search -->
    <div class="search-popup">
        <button class="close-search style-1"><i class="fa fa-times"></i></button>
        <button class="close-search"><i class="fas fa-arrow-up"></i></button>
        <form method="post" action="#">
            <div class="form-group">
                <input id="search1" type="search" name="search-field" value="" placeholder="Search..." required="">
                <button type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
    <!-- End Header Search -->

    <!--========  Start Sidebar Area ========-->
    <div id="sidebar-area" class="sidebar">
        <div class="sidebar-overlay"></div>
        <div class="sidebar-wrapper">
            <button class="sidebar-close-btn">
                <svg class="icon-close" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                    x="0px" y="0px" width="16px" height="12.7px" viewBox="0 0 16 12.7"
                    style="enable-background:new 0 0 16 12.7" xml:space="preserve">
                    <g>
                        <rect x="0" y="5.4" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -2.1569 7.5208)" width="16"
                            height="2"></rect>
                        <rect x="0" y="5.4" transform="matrix(0.7071 0.7071 -0.7071 0.7071 6.8431 -3.7929)" width="16"
                            height="2"></rect>
                    </g>
                </svg>
            </button>
            <div class="sidebar-content">
                <div class="sidebar-logo">
                    <a class="dark-logo" href="{{ route('home') }}"><img src="{{ asset('logo.png') }}" alt="logo"></a>
                </div>
                <div class="sidebar-menu-wrap"></div>
                <div class="sidebar-about">
                    <h6>Explore the world</h6>
                    <div class="sidebar-header">
                        <h3>World's leading Business agency</h3>
                    </div>
                </div>
                <!-- Instagram Feed Section -->
                <div class="instafeed-wrapper">
                    <div class="insta-item">
                        <a href="https://www.instagram.com" target="_blank">
                            <img src="assets/images/sidebar/sidebar1.jpeg" alt="">
                            <span class="overlay"><i class="fa-brands fa-instagram"></i></span>
                        </a>
                    </div>
                    <div class="insta-item">
                        <a href="https://www.instagram.com" target="_blank">
                            <img src="assets/images/sidebar/sidebar-2.jpg" alt="">
                            <span class="overlay"><i class="fa-brands fa-instagram"></i></span>
                        </a>
                    </div>
                    <div class="insta-item">
                        <a href="https://www.instagram.com" target="_blank">
                            <img src="assets/images/sidebar/sidebar-3.jpg" alt="">
                            <span class="overlay"><i class="fa-brands fa-instagram"></i></span>
                        </a>
                    </div>
                    <div class="insta-item">
                        <a href="https://www.instagram.com" target="_blank">
                            <img src="assets/images/sidebar/sidebar-4.jpg" alt="">
                            <span class="overlay"><i class="fa-brands fa-instagram"></i></span>
                        </a>
                    </div>
                    <div class="insta-item">
                        <a href="https://www.instagram.com" target="_blank">
                            <img src="assets/images/sidebar/sidebar-5.jpg" alt="">
                            <span class="overlay"><i class="fa-brands fa-instagram"></i></span>
                        </a>
                    </div>
                    <div class="insta-item">
                        <a href="https://www.instagram.com" target="_blank">
                            <img src="assets/images/sidebar/sidebar-6.jpg" alt="">
                            <span class="overlay"><i class="fa-brands fa-instagram"></i></span>
                        </a>
                    </div>
                </div>
                <!-- mail submit -->
                <p class="text-center mt-40">Get latest update for our trusted applications</p>
                <form class="newsletter-form" action="https://formspree.io/f/mzbnjrnb" method="post">
                    <div class="form-group">
                        <input type="email" name="email" class="email" value="" placeholder="Enter Your Email"
                            autocomplete="on" required="">
                        <button type="submit">
                            <i class="far fa-paper-plane"></i>
                            <span class="btn-title"></span>
                        </button>
                    </div>
                </form>

                <ul class="sidebar-social">
                    <li class="facebook"><a href="https://www.facebook.com/sirateq" target="_blank"
                            rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a></li>
                    <li class="instagram"><a href="https://www.instagram.com/sirateq_ghana/" target="_blank"
                            rel="noopener noreferrer"><i class="fab fa-instagram"></i></a></li>
                    <li class="twitter"><a href="https://x.com/sirateq_ghana" target="_blank"
                            rel="noopener noreferrer"><i class="fab fa-twitter"></i></a></li>
                    <li class="linkedin"><a href="https://www.linkedin.com/company/sirateq_ghana" target="_blank"
                            rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
    <!--======== / Sidebar Area ========-->


</header>