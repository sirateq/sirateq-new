<x-layouts.app title="Technology That Powers Growth">

    <x-hero />

    <x-services-list />

    <x-why-choose-us />

    <x-home-products />



    <div class="tv-counter-section style-2 bg-light position-relative z-1">
        <div class="counter-inner lg-br-0 py-65 lg-py-40 position-relative mx-30 xxl-mx-0 overflow-hidden">
            <div class="bg image"><img src="{{ asset('assets/images/counter/hm1-bg01.webp') }}" alt=""></div>
            <div class="overlay bg-theme mbm-overlay"></div>
            <div class="container">
                <div class="row gy-30">
                    <div class="col-lg-4 col-md-6">
                        <div class="counter-box">
                            <div class="icon"><img src="{{ asset('assets/images/counter/hm1-icon01.webp') }}"
                                    alt="Icon"></div>
                            <div class="content">
                                <h4 class="title mb-0"><span class="count-number odometer" data-count="52">0</span>+
                                </h4>
                                <h6 class="text mb-0">Successful Projects</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="counter-box justify-content-center ustify-content-md-center">
                            <div class="icon"><img src="{{ asset('assets/images/counter/hm1-icon02.webp') }}"
                                    alt="Icon"></div>
                            <div class="content">
                                <h4 class="title mb-0"><span class="count-number odometer" data-count="12">0</span>+
                                </h4>
                                <h6 class="text mb-0">All Awards Winning</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="counter-box align-items-start align-items-lg-end">
                            <div class="icon"><img src="{{ asset('assets/images/counter/hm1-icon03.webp') }}"
                                    alt="Icon"></div>
                            <div class="content">
                                <h4 class="title mb-0"><span class="count-number odometer" data-count="96">0</span>%
                                </h4>
                                <h6 class="text mb-0">Satisfaction Rates</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
