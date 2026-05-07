<section class="w-full">
    <x-admin.layout :heading="__('Category Form')" :subheading="__('Create or update category')">
        <form wire:submit="save" class="space-y-4 max-w-xl">
            <flux:input wire:model="name" :label="__('Name')" required />
            <flux:textarea wire:model="description" :label="__('Description')" />
            <flux:switch wire:model="is_active" :label="__('Active')" />
            <flux:button type="submit" variant="primary">{{ __('Save Category') }}</flux:button>
        </form>
    </x-admin.layout>
</section>
