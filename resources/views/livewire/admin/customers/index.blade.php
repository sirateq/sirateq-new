<section class="w-full">
    <x-admin.layout :heading="__('Customers')" :subheading="__('People with an account who are not dashboard admins')" icon="users">
        <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="border-b border-zinc-200/70 p-4 dark:border-zinc-700/60">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search by name or email')" class="sm:max-w-xs" />
            </div>

            <flux:table :paginate="$this->customers">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Customer') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">{{ __('Email') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'orders_count'" :direction="$sortDirection" wire:click="sort('orders_count')" align="end">{{ __('Orders') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('Joined') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->customers as $customer)
                        <flux:table.row :key="'customer-'.$customer->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" :name="$customer->name" />
                                    <flux:heading class="!text-sm">{{ $customer->name }}</flux:heading>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-600 dark:text-zinc-300">{{ $customer->email }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $customer->orders_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap text-sm text-zinc-500">{{ $customer->created_at?->diffForHumans() }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:tooltip :content="__('View profile & orders')">
                                    <flux:button variant="ghost" size="sm" icon="eye" :href="route('admin.customers.show', $customer)" wire:navigate />
                                </flux:tooltip>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <div class="flex flex-col items-center gap-2 py-10 text-center">
                                    <flux:icon name="users" class="size-8 text-zinc-400" />
                                    <flux:heading>{{ __('No customers yet') }}</flux:heading>
                                    <flux:text>{{ __('Customers appear after they register—guest checkout orders stay under guests in order details.') }}</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </x-admin.layout>
</section>
