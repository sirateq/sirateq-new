<!--==============================
Hero Section Six
==============================-->
@php
    $heroSlide = ['class' => 'pageTurn', 'bg' => 'assets/images/hero/118153.jpg'];

    $heroContent = [
        'tagline' => 'Empowering Businesses Through Technology',
        'title' => 'Technology That <br> Powers Growth',
        'description' => 'End-to-end technology solutions designed to drive innovation, security, and business growth.',
    ];
@endphp

<section class="tv-hero-section style-6 overflow-hidden z-2 bg-light">
    <div class="hero-inner position-relative">
        <div class="container-fluid px-0">
            <div class="hero-slider-2 position-relative swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide {{ $heroSlide['class'] }}">
                        <div class="hero-area position-relative">
                            <div class="bg image" data-bg-src="{{ $heroSlide['bg'] }}"></div>
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="hero-content">
                                            <span class="sub-title">
                                                <img src="assets/images/hero/check2.webp" alt="Technology icon">{{ $heroContent['tagline'] }}
                                            </span>
                                            <h1 class="hero-title text-white">{!! $heroContent['title'] !!}</h1>
                                            <div class="text-icon position-relative">
                                                <p class="text">{{ $heroContent['description'] }}</p>
                                            </div>
                                            <div class="border my-50"></div>
                                            <div class="hero-user">
                                                <a href="{{ route('about-us') }}" class="theme-btn br-30">
                                                    <span class="link-effect">
                                                        <span class="effect-1">Learn More</span>
                                                        <span class="effect-1">Learn More</span>
                                                    </span>
                                                {{-- <span class="arrow-all">
                                                    <i>
                                                        <svg width="16" height="19" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2 6H10M10 6L6 2M10 6L6 10" stroke="#1053f3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                        <svg width="16" height="19" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2 6H10M10 6L6 2M10 6L6 10" stroke="#1053f3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </i>
                                                </span> --}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hero-btn-wrapper">
                        <div class="array-button">
                            <button class="array-prev"><i class="fa fa-arrow-left-long"></i></button>
                            <button class="array-next active"><i class="fa fa-arrow-right-long"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
