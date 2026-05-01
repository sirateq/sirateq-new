@php
    $services = [
        [
            'image' => 'assets/images/119368.jpg',
            'icon' => 'assets/images/service/hm6-icon01.webp',
            'title' => 'Web Development &amp; Design',
            'description' => 'We build modern, responsive websites and web applications that enhance user experience and support business growth.',
        ],
        [
            'image' => 'assets/images/80711.jpg',
            'icon' => 'assets/images/service/hm6-icon02.webp',
            'title' => 'Mobile Application Development',
            'description' => 'We design and develop intuitive mobile apps that improve customer engagement and streamline business operations.',
        ],
        [
            'image' => 'assets/images/120535.jpg',
            'icon' => 'assets/images/service/hm6-icon03.webp',
            'title' => 'IT Consultation &amp; Advisory',
            'description' => 'We provide strategic technology guidance to help organizations optimize systems, improve efficiency, and make informed decisions.',
        ],
        [
            'image' => 'assets/images/126352.jpg',
            'icon' => 'assets/images/service/hm6-icon04.webp',
            'title' => 'Cloud Services',
            'description' => 'We deliver scalable cloud deployment, migration, and infrastructure management solutions that ensure reliability and security.',
        ],
        [
            'image' => 'assets/images/136442.jpg',
            'icon' => 'assets/images/service/hm6-icon01.webp',
            'title' => 'Data Intelligence &amp; IoT Solutions',
            'description' => 'We help businesses collect, analyze, and utilize data from connected devices to automate operations and drive smarter decisions.',
        ],
        [
            'image' => 'assets/images/8970.jpg',
            'icon' => 'assets/images/service/hm6-icon03.webp',
            'title' => 'Specialized Technology Solutions',
            'description' => 'We develop custom software and tailored technology systems to solve unique business challenges.',
        ],
    ];
@endphp

<section class="tv-service-section bg-light style-6">
    <div class="tv-service-inner space position-relative overflow-hidden bg-light2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="service-title-area d-flex justify-content-between sm-flex-column sm-mb-30">
                        <!-- Section Title -->
                        <div class="title-wrap " data-wow-duration="1.5s" data-wow-delay=".4s">
                            <h2 class="sec-title ">Solutions We Offer</h2>
                        </div>
                        <div class="service-btn-wrapper">
                            <div class="array-button">
                                <button class="array-prev"><i class="fa fa-arrow-left-long"></i></button>
                                <button class="array-next active"><i class="fa fa-arrow-right-long"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row gy-30">
                <div class="col-lg-12"> 
                    <div class="service-slider swiper">
                        <div class="swiper-wrapper">
                            @foreach ($services as $service)
                                <div class="swiper-slide">
                                    <div class="service-box-six">
                                        <div class="inner">
                                            <div class="image-box">
                                                <div class="thumb" style="aspect-ratio: 16 / 10; overflow: hidden;">
                                                    <img
                                                        src="{{ $service['image'] }}"
                                                        alt="Service"
                                                        width="640"
                                                        height="400"
                                                        loading="lazy"
                                                        decoding="async"
                                                        style="width: 100%; height: 100%; object-fit: cover; display: block;"
                                                    >
                                                </div>
                                                <div class="content">
                                                    <div class="icon-inner">
                                                        <div class="icon"><img src="{{ $service['icon'] }}" alt="Service icon"></div>
                                                        <h4 class="text">{!! $service['title'] !!}</h4>
                                                    </div> 
                                                    <div class="border my-25"></div>
                                                    <a href="{{ route('services') }}" class="theme-btn style2 br-30">
                                                        <span class="link-effect">
                                                            <span class="effect-1">VIEW DETAILS</span>
                                                            <span class="effect-1">VIEW DETAILS</span>
                                                        </span>
                                                        <span class="arrow-all-2">
                                                            <i>
                                                                <svg width="10" height="10" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.0035 3.90804L1.41153 12.5L0 11.0885L8.59097 2.49651H1.01922V0.5H12V11.4808H10.0035V3.90804Z"></path>
                                                                </svg>
                                                                <svg width="10" height="10" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.0035 3.90804L1.41153 12.5L0 11.0885L8.59097 2.49651H1.01922V0.5H12V11.4808H10.0035V3.90804Z"></path>
                                                                </svg>
                                                            </i>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>