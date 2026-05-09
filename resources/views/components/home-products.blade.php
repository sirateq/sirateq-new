@php
    $projects = [
        [
            'thumb' => 'assets/images/products/pollvite.jpg',
            'title' => 'Pollvite',
            'yearType' => 'Events & Voting',
            'badges' => ['SaaS', 'Events'],
            'link' => 'https://pollvite.com',
            'description' => 'Smart event and polling platform built to boost engagement and streamline coordination.',
        ],
        [
            'thumb' => 'assets/images/products/sendazi.jpg',
            'title' => 'Sendazi',
            'yearType' => 'Communication',
            'badges' => ['SaaS', 'API'],
            'link' => 'https://sendazi.com',
            'description' => 'Communication platform with API-ready services for scalable business messaging.',
        ],
        [
            'thumb' => 'assets/images/products/erp.png',
            'title' => 'All-in-One ERP',
            'yearType' => 'Enterprise',
            'badges' => ['Software', 'Management'],
            'link' => 'https://erp.sirateq.com',
            'description' => 'Unified ERP solution to manage finance, operations, teams, and reporting in one place.',
        ],
        // [
        //     'thumb' => 'assets/images/project/hm6-img04.webp',
        //     'title' => 'Hotel Management',
        //     'yearType' => 'Hospitality',
        //     'badges' => ['Software', 'Management'],
        //     'description' => 'End-to-end hospitality system for bookings, operations, guest experience, and billing.',
        // ],
        // [
        //     'thumb' => 'assets/images/project/hm6-img01.webp',
        //     'title' => 'HR &amp; PAYROLL SOFTWARE',
        //     'yearType' => 'HR',
        //     'badges' => ['Payroll', 'Software'],
        //     'description' => 'Reliable HR and payroll automation for employee records, attendance, and salary workflows.',
        // ],
        // [
        //     'thumb' => 'assets/images/project/hm6-img02.webp',
        //     'title' => 'Restaurant Management System',
        //     'yearType' => 'Restaurant',
        //     'badges' => ['Management', 'System'],
        //     'description' => 'Restaurant software for menu, orders, POS, inventory, kitchen flow, and reporting.',
        // ],
    ];
@endphp

<section class="tv-project-section space bg-light">
    <div class="container">
        <!-- Section Title -->
        <div class="row">
            <div class="col-lg-12">
                <div class="project-title-area d-flex  sm-mb-30">
                    <div class="title-wrap white">
                        <div class="sub-title-2 text-theme"><i class="fa-solid fa-circle-check"></i>Products</div>
                        <h2 class="sec-title text-dark">Our Digital Products & Software Solutions</h2>
                    </div>
                    <div class="project-btn sm-justify-content-start">
                        <a href="{{ route('products') }}" class="theme-btn">
                            <span class="link-effect">
                                <span class="effect-1">Discover More</span>
                                <span class="effect-1">Discover More</span>
                            </span>
                            <span class="arrow-all">
                                <i>
                                    <svg width="16" height="19" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 6H10M10 6L6 2M10 6L6 10" stroke="#1053f3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <svg width="16" height="19" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 6H10M10 6L6 2M10 6L6 10" stroke="#1053f3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid px-60 ml-px-15 xxl-px-50">
        <div class="row gy-30">
            @foreach ($projects as $project)
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                    <div class="tv-project-single-box">
                        <div class="image-wrapper" style="aspect-ratio: 16 / 10; overflow: hidden;">
                            <img
                                src="{{ $project['thumb'] }}"
                                alt="{{ $project['title'] }}"
                                width="640"
                                height="400"
                                loading="lazy"
                                decoding="async"
                                style="width: 100%; height: 100%; object-fit: cover; display: block;"
                            >
                        </div>
                        <div class="project-info">
                            <span class="tag">{{ strtoupper($project['yearType']) }}</span>
                            <h3 class="title"><a href="{{ $project['link'] }}" target="_blank">{!! $project['title'] !!}</a></h3>
                            <div class="border mb-20"></div>
                            <p>{{ $project['description'] }}</p>
                            <div class="icon-box">
                                <a href="{{ $project['link'] }}" target="_blank" class="hover-icon"><i class="fa-regular fa-arrow-up-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

