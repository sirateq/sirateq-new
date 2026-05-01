@php
    $whyChooseItems = [
        [
            'target' => 'img1',
            'icon' => 'assets/images/choose/hm5-icon01.webp',
            'tag' => 'Product Innovation',
            'title' => 'Purpose-Built Digital Solutions',
            'description' => 'We design and build practical software products like Pollvite, Sendazi, ERP platforms, and industry-specific systems that solve real business problems.',
            'image' => 'assets/images/choose/hm5-img01.webp',
            'iconClass' => '',
        ],
        [
            'target' => 'img2',
            'icon' => 'assets/images/choose/hm5-icon02.webp',
            'tag' => 'Engineering Team',
            'title' => 'Experienced Technology Experts',
            'description' => 'Our team combines product strategy, UI/UX, software engineering, cloud architecture, and integration expertise to deliver reliable, scalable solutions.',
            'image' => 'assets/images/choose/hm5-img02.webp',
            'iconClass' => '',
        ],
        [
            'target' => 'img3',
            'icon' => 'assets/images/choose/hm5-icon03.webp',
            'tag' => 'Long-Term Partnership',
            'title' => 'Reliable Support & Continuous Improvement',
            'description' => 'Beyond launch, we provide ongoing support, optimization, and advisory services to keep your systems secure, efficient, and aligned with business growth.',
            'image' => 'assets/images/choose/hm5-img03.webp',
            'iconClass' => 'border-none',
        ],
    ];


    $clientImagePaths = glob(public_path('assets/images/clients/*.{jpg,jpeg,png,webp,gif,svg}'), GLOB_BRACE) ?: [];
    sort($clientImagePaths);

    $brandImages = array_map(
        static fn (string $imagePath): string => asset('assets/images/clients/' . basename($imagePath)),
        $clientImagePaths
    );
@endphp

<section class="tv-choose-section style-5 space-top overflow-hidden position-relative">
    <div class="bg image"><img src="assets/images/choose/hm5-bg01.webp" alt=""></div>
    <div class="container space-bottom">
        <div class="row gy-30 align-items-center">
            <div class="col-lg-12">
                <div class="choose-title-area d-flex justify-content-between sm-flex-column sm-mb-30">
                    <div class="title-wrap three" data-wow-duration="1.5s" data-wow-delay=".4s">
                        <div class="sub-title-2 two text-white"><i class="fa-solid fa-circle-check"></i>Why Choose Us</div>
                    </div>
                    <div class="title-wrap three" data-wow-duration="1.5s" data-wow-delay=".4s">
                        <h2 class="sec-title text-white">Why businesses trust us for<br>digital transformation</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-30">
            <div class="col-lg-6">
                <div class="choose-left">
                    @foreach ($whyChooseItems as $item)
                        <div class="title-box {{ $loop->first ? 'active' : '' }}" data-target="{{ $item['target'] }}">
                            <div class="icon {{ $item['iconClass'] }}"><img src="{{ $item['icon'] }}" alt=""></div>
                            <div class="content">
                                <span>{{ $item['tag'] }}</span>
                                <h4 class="title">{{ $item['title'] }}</h4>
                                <p class="description">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="choose-right">
                    @foreach ($whyChooseItems as $item)
                        <img src="{{ $item['image'] }}" class="{{ $loop->first ? 'active' : '' }}" id="{{ $item['target'] }}" alt="">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="container pt-50 xs-pt-20 space-bottom position-relative pos">
        <div class="tv-brands-section style-3 position-relative z-3">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sponsors-outer brand-outher">
                            <div class="brands-slider-three swiper">
                                <div class="swiper-wrapper">
                                    @foreach ($brandImages as $index => $brandImage)
                                        <div class="swiper-slide">
                                            <div class="brand-item">
                                                <a class="image" href="#">
                                                    <img src="{{ $brandImage }}" alt="Brand {{ $index + 1 }}">
                                                    <img src="{{ $brandImage }}" alt="Brand {{ $index + 1 }}">
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
