<section class="w-full">
    <x-admin.layout :heading="__('Product Form')" :subheading="__('Create or update product and inventory')">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <flux:input wire:model="name" :label="__('Product name')" required />
            <flux:input wire:model="slug" :label="__('Slug')" required />
            <flux:select wire:model="category_id" :label="__('Category')" required>
                <flux:select.option value="">{{ __('Select category') }}</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:textarea wire:model="description" :label="__('Description')" class="md:col-span-2" />
            <flux:input wire:model="variant_name" :label="__('Variant name')" required />
            <flux:input wire:model="sku" :label="__('SKU')" required />
            <flux:input wire:model="price" type="number" step="0.01" min="0" :label="__('Price')" required />
            <flux:input wire:model="quantity" type="number" min="0" :label="__('Stock quantity')" required />
            <div class="md:col-span-2">
                <flux:button type="submit" variant="primary">{{ __('Save Product') }}</flux:button>
            </div>
        </form>
    </x-admin.layout>
</section>
