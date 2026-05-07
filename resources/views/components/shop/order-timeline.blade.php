@props(['order'])

@php
    $steps = $order->trackingTimeline();
@endphp

<div style="background: #fff; border-radius: 16px; padding: 24px 28px; box-shadow: 0 1px 2px rgba(6, 17, 83, 0.04), 0 4px 16px rgba(6, 17, 83, 0.08); border: 1px solid #e5e7eb; margin-bottom: 24px;">
    <h2 style="font-size: 18px; font-weight: 700; color: #061153; margin: 0 0 6px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-route" style="color: #1053f3;"></i>
        {{ __('Order tracking') }}
    </h2>
    <p style="margin: 0 0 22px; font-size: 13px; color: #6b7280;">{{ __('Current progress for your order.') }}</p>

    <ol style="list-style: none; margin: 0; padding: 0;">
        @foreach ($steps as $idx => $step)
            @php
                $isComplete = $step['state'] === 'complete';
                $isCurrent = $step['state'] === 'current';
                $isLast = $idx === count($steps) - 1;
                $dotBg = $isComplete ? '#059669' : ($isCurrent ? '#1053f3' : '#e5e7eb');
                $labelColor = $isComplete || $isCurrent ? '#061153' : '#9ca3af';
            @endphp
            <li style="display: flex; gap: 14px; margin: 0; padding: 0 0 {{ $isLast ? '0' : '18px' }}; position: relative;">
                @if (! $isLast)
                    <span style="position: absolute; left: 15px; top: 36px; bottom: -4px; width: 2px; background: {{ $isComplete ? 'rgba(5, 150, 105, 0.35)' : '#e5e7eb' }}; border-radius: 1px;"></span>
                @endif
                <div style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 999px; background: {{ $dotBg }}; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; z-index: 1; box-shadow: 0 2px 8px rgba(6, 17, 83, 0.12);">
                    @if ($isComplete)
                        <i class="fa-solid fa-check"></i>
                    @elseif ($isCurrent)
                        <i class="fa-solid fa-ellipsis" style="animation: pulse-dot 1.2s ease-in-out infinite;"></i>
                    @else
                        <i class="fa-regular fa-circle" style="opacity: 0.5; font-size: 10px;"></i>
                    @endif
                </div>
                <div style="min-width: 0; padding-top: 2px;">
                    <p style="margin: 0; font-size: 15px; font-weight: 700; color: {{ $labelColor }};">
                        {{ $step['label'] }}
                        @if ($isCurrent)
                            <span style="margin-left: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #1053f3;">{{ __('In progress') }}</span>
                        @endif
                    </p>
                    <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280; line-height: 1.45;">{{ $step['description'] }}</p>
                    @if (! empty($step['date']))
                        <p style="margin: 8px 0 0; font-size: 12px; color: #9ca3af;">
                            {{ $step['date']->timezone(config('app.timezone'))->format('M j, Y · g:i A') }}
                        </p>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</div>

<style>
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.45; }
    }
</style>
