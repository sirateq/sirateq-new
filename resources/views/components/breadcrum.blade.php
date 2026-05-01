@props([
    'title' => 'Services',
    'image' => 'assets/images/server-woman.png',
    'description' => 'Building Digital Momentum for Africa’s Forward-Thinking Brands',
])
<section class="tv-breadcrumb-section">
    <div class="tv-breadcrumb-inner mx-30 ml-mx-0 position-relative overflow-hidden br-30 ml-br-0">
        <div class="bg image"><img src="{{ $image }}" alt=""></div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="title-outer">
                        <div class="page-title">
                            <h2 class="title">
                                {!! $title !!}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>