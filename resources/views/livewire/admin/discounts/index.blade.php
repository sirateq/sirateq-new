<section class="w-full">
    <x-admin.layout :heading="__('Discounts')" :subheading="__('Create and manage coupon codes')">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-3">
            <flux:input wire:model="code" :label="__('Coupon Code')" required />
            <flux:input wire:model="name" :label="__('Name')" required />
            <flux:input wire:model="discount_percentage" type="number" min="1" max="95" :label="__('Discount %')" required />
            <div class="md:col-span-3">
                <flux:button type="submit" variant="primary">{{ __('Save Coupon') }}</flux:button>
            </div>
        </form>

        <div class="mt-6">
            <flux:table :paginate="$this->coupons">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'code'" :direction="$sortDirection" wire:click="sort('code')">{{ __('Code') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'discount_percentage'" :direction="$sortDirection" wire:click="sort('discount_percentage')">{{ __('Discount') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection" wire:click="sort('is_active')">{{ __('Status') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->coupons as $coupon)
                        <flux:table.row :key="$coupon->id">
                            <flux:table.cell variant="strong">{{ $coupon->code }}</flux:table.cell>
                            <flux:table.cell>{{ $coupon->name }}</flux:table.cell>
                            <flux:table.cell>{{ $coupon->discount_percentage }}%</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$coupon->is_active ? 'green' : 'zinc'" inset="top bottom">
                                    {{ $coupon->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </x-admin.layout>
</section>
