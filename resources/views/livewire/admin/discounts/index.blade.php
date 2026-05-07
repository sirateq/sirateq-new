<section class="w-full">
    <x-admin.layout :heading="__('Discounts')" :subheading="__('Create and manage promotional coupon codes')" icon="ticket">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <header class="mb-4 flex items-center gap-2">
                        <flux:icon name="plus-circle" class="size-5 text-zinc-500" />
                        <flux:heading size="lg">{{ __('New coupon') }}</flux:heading>
                    </header>
                    <flux:separator class="mb-5" />

                    <form wire:submit="save" class="space-y-4">
                        <flux:input wire:model="code" :label="__('Coupon code')" :placeholder="__('SUMMER20')" required />
                        <flux:input wire:model="name" :label="__('Display name')" :placeholder="__('Summer Sale 20%')" required />
                        <flux:input wire:model="discount_percentage" type="number" min="1" max="95" :label="__('Discount %')" icon="percent-badge" required />
                        <flux:button type="submit" variant="primary" icon="check" class="w-full" wire:loading.attr="disabled">
                            {{ __('Save coupon') }}
                        </flux:button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="border-b border-zinc-200/70 p-4 dark:border-zinc-700/60">
                        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search coupons')" class="sm:max-w-xs" />
                    </div>

                    <flux:table :paginate="$this->coupons">
                        <flux:table.columns>
                            <flux:table.column sortable :sorted="$sortBy === 'code'" :direction="$sortDirection" wire:click="sort('code')">{{ __('Code') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'discount_percentage'" :direction="$sortDirection" wire:click="sort('discount_percentage')" align="end">{{ __('Off') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection" wire:click="sort('is_active')">{{ __('Status') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($this->coupons as $coupon)
                                <flux:table.row :key="$coupon->id">
                                    <flux:table.cell>
                                        <div class="flex items-center gap-3">
                                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                                <flux:icon name="ticket" class="size-4 text-zinc-500" />
                                            </div>
                                            <span class="font-mono text-sm font-semibold">{{ $coupon->code }}</span>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $coupon->name }}</flux:table.cell>
                                    <flux:table.cell variant="strong" align="end">−{{ $coupon->discount_percentage }}%</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" :color="$coupon->is_active ? 'green' : 'zinc'" :icon="$coupon->is_active ? 'check-circle' : 'minus-circle'" inset="top bottom">
                                            {{ $coupon->is_active ? __('Active') : __('Inactive') }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell align="end">
                                        <flux:tooltip :content="$coupon->is_active ? __('Disable') : __('Enable')">
                                            <flux:button variant="ghost" size="sm" :icon="$coupon->is_active ? 'eye-slash' : 'eye'" wire:click="toggleStatus({{ $coupon->id }})" />
                                        </flux:tooltip>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="5">
                                        <div class="flex flex-col items-center gap-2 py-10 text-center">
                                            <flux:icon name="ticket" class="size-8 text-zinc-400" />
                                            <flux:heading>{{ __('No coupons yet') }}</flux:heading>
                                            <flux:text>{{ __('Create your first coupon to start offering discounts.') }}</flux:text>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
            </div>
        </div>
    </x-admin.layout>
</section>
