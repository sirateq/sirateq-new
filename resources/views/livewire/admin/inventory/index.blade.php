<section class="w-full">
    <x-admin.layout :heading="__('Inventory')" :subheading="__('Adjust on-hand stock for product variants')" icon="archive-box">
        <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 border-b border-zinc-200/70 p-4 dark:border-zinc-700/60 sm:flex-row sm:items-center sm:justify-between">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search by product or SKU')" class="sm:max-w-xs" />
                <flux:select wire:model.live="stockFilter" class="sm:max-w-xs">
                    <flux:select.option value="">{{ __('All items') }}</flux:select.option>
                    <flux:select.option value="low">{{ __('Low stock') }}</flux:select.option>
                    <flux:select.option value="out">{{ __('Out of stock') }}</flux:select.option>
                </flux:select>
            </div>

            <flux:table :paginate="$this->items">
                <flux:table.columns>
                    <flux:table.column>{{ __('Product') }}</flux:table.column>
                    <flux:table.column>{{ __('SKU') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'quantity'" :direction="$sortDirection" wire:click="sort('quantity')">{{ __('On hand') }}</flux:table.column>
                    <flux:table.column>{{ __('Stock state') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Adjust') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->items as $item)
                        @php($state = $item->quantity === 0 ? 'out' : ($item->quantity <= $item->low_stock_threshold ? 'low' : 'ok'))
                        <flux:table.row :key="$item->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="cube" class="size-4 text-zinc-500" />
                                    </div>
                                    <div>
                                        <flux:heading class="!text-sm">{{ $item->variant->product->name }}</flux:heading>
                                        <flux:text size="sm" class="text-zinc-500">{{ $item->variant->name }}</flux:text>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs">{{ $item->variant->sku }}</flux:table.cell>
                            <flux:table.cell variant="strong">{{ $item->quantity }}</flux:table.cell>
                            <flux:table.cell>
                                @if ($state === 'out')
                                    <flux:badge size="sm" color="red" icon="exclamation-triangle" inset="top bottom">{{ __('Out of stock') }}</flux:badge>
                                @elseif ($state === 'low')
                                    <flux:badge size="sm" color="amber" icon="exclamation-circle" inset="top bottom">{{ __('Low stock') }}</flux:badge>
                                @else
                                    <flux:badge size="sm" color="green" icon="check-circle" inset="top bottom">{{ __('In stock') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <div class="inline-flex items-center overflow-hidden rounded-lg border border-zinc-200/70 dark:border-zinc-700/60">
                                    <flux:tooltip :content="__('Decrease')">
                                        <flux:button variant="ghost" size="sm" icon="minus" wire:click="adjust({{ $item->id }}, -1)" />
                                    </flux:tooltip>
                                    <flux:tooltip :content="__('Increase')">
                                        <flux:button variant="ghost" size="sm" icon="plus" wire:click="adjust({{ $item->id }}, 1)" />
                                    </flux:tooltip>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <div class="flex flex-col items-center gap-2 py-10 text-center">
                                    <flux:icon name="archive-box" class="size-8 text-zinc-400" />
                                    <flux:heading>{{ __('No inventory found') }}</flux:heading>
                                    <flux:text>{{ __('Add products to populate inventory.') }}</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-admin.layout>
</section>
