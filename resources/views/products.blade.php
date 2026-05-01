<x-layouts.app title="Products">
    <x-breadcrum title="Our Digital Products & Software Solutions" />

    @php
        $products = [
            [
                'image' => 'assets/images/products/pollvite.jpg',
                'name' => 'Pollvite',
                'category' => 'Events & Voting',
                'description' => 'Smart event and polling platform built to boost engagement and streamline coordination.',
                'link' => 'pollvite',
            ],
            [
                'image' => 'assets/images/products/sendazi.jpg',
                'name' => 'Sendazi',
                'category' => 'Communication',
                'description' => 'Communication platform with API-ready services for scalable business messaging.',
                'link' => 'sendazi',
            ],
            [
                'image' => 'assets/images/products/erp.png',
                'name' => 'All-in-One ERP',
                'category' => 'Enterprise',
                'description' => 'Unified ERP solution to manage finance, operations, teams, and reporting in one place.',
                'link' => 'erp',
            ],
            [
                'image' => 'assets/images/products/hotel.png',
                'name' => 'Hotel Management',
                'category' => 'Hospitality',
                'description' => 'End-to-end hospitality system for bookings, operations, guest experience, and billing.',
                'link' => 'hotel',
            ],
            [
                'image' => 'assets/images/products/hr.png',
                'name' => 'HR & PAYROLL SOFTWARE',
                'category' => 'HR',
                'description' => 'Reliable HR and payroll automation for employee records, attendance, and salary workflows.',
                'link' => 'hr',
            ],
            [
                'image' => 'assets/images/products/restaurant.png',
                'name' => 'Restaurant Management System',
                'category' => 'Restaurant',
                'description' => 'Restaurant software for menu, orders, POS, inventory, kitchen flow, and reporting.',
                'link' => 'restaurant',
            ],
        ];
    @endphp

    <section class="tv-project-section inner space bg-light">
        <div class="container">
            <div class="title-wrap text-center" data-wow-duration="1.5s" data-wow-delay=".4s">
                <div class="sub-title-2 text-theme"><i class="fa-solid fa-circle-check"></i>Products</div>
                <h2 class="sec-title">Our Digital Products</h2>
                <p>Software solutions built to solve practical business challenges.</p>
            </div>

            <div class="row gy-40 mt-20">
                @foreach ($products as $product)
                    <div class="col-lg-4 col-md-6">
                        <div class="project-single-box h-100">
                            <div class="thumb" style="aspect-ratio: 16 / 10; overflow: hidden;">
                                <img
                                    class="img"
                                    src="{{ $product['image'] }}"
                                    alt="{{ $product['name'] }}"
                                    width="640"
                                    height="400"
                                    loading="lazy"
                                    decoding="async"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                >
                            </div>
                            <div class="project-info">
                                <h4 class="title"><a href="#">{{ $product['name'] }}</a></h4>
                                <div class="project-badge">
                                    <span>{{ strtoupper($product['category']) }}</span>
                                </div>
                                <p class="mt-15 mb-0">{{ $product['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <x-cta />
</x-layouts.app>
