@props([
    'image' => 'assets/images/server-woman.png',
    'height' => null,
    'title' => null,
    'breadcrumbs' => [],
])

@php
    $resolvedHeight = $height ?? ($title ? '300px' : '110px');
@endphp

<section class="tv-breadcrumb-section tv-breadcrumb-slim" style="margin-bottom: 0;">
    <div class="tv-breadcrumb-inner mx-30 ml-mx-0 position-relative overflow-hidden br-30 ml-br-0"
         style="height: {{ $resolvedHeight }};">
        <div class="bg image" style="position: absolute; inset: 0;">
            <img src="{{ $image }}" alt=""
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.55), rgba(0,0,0,0.25));"></div>

        @if ($title)
            <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; padding: 32px 16px 36px; text-align: center; color: #fff;">
                <h1 style="font-size: clamp(28px, 5vw, 44px); font-weight: 700; margin: 0 0 12px; color: #fff; line-height: 1.1;">
                    {!! $title !!}
                </h1>

                @if (! empty($breadcrumbs))
                    <nav aria-label="breadcrumb" style="font-size: 14px; color: rgba(255,255,255,0.85);">
                        @foreach ($breadcrumbs as $crumb)
                            @if (! empty($crumb['url']) && ! $loop->last)
                                <a href="{{ $crumb['url'] }}" wire:navigate
                                   style="color: inherit; text-decoration: none;">
                                    {{ $crumb['label'] }}
                                </a>
                            @else
                                <span style="color: #fff;">{{ $crumb['label'] }}</span>
                            @endif

                            @if (! $loop->last)
                                <span style="margin: 0 8px;">—</span>
                            @endif
                        @endforeach
                    </nav>
                @endif
            </div>
        @endif
    </div>
</section>
