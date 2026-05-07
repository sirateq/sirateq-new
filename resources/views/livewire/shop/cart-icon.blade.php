<a href="{{ route('shop.cart') }}"
   wire:navigate
   title="{{ __('Cart') }}"
   aria-label="{{ __('View cart') }}"
   style="position: relative; display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; margin-left: 16px; border-radius: 9999px; background-color: #ffffff; color: #061153; box-shadow: 0 4px 14px rgba(6, 17, 83, 0.12); transition: transform .2s ease, box-shadow .2s ease;"
   onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(6,17,83,0.18)'"
   onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 14px rgba(6, 17, 83, 0.12)'">
    <i class="fa-solid fa-cart-shopping" style="font-size: 18px;"></i>
    @if ($this->itemCount > 0)
        <span style="position: absolute; top: -4px; right: -4px; min-width: 22px; height: 22px; padding: 0 6px; border-radius: 9999px; background: #061153; color: #ffffff; font-size: 11px; font-weight: 700; line-height: 1; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(6, 17, 83, 0.3);">
            {{ $this->itemCount > 99 ? '99+' : $this->itemCount }}
        </span>
    @endif
</a>
