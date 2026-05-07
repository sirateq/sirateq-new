<section class="w-full">
    <x-admin.layout :heading="__('Admin users')" :subheading="__('Invite teammates who can manage the store dashboard')" icon="shield-check">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="rounded-xl border border-zinc-200/70 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <header class="mb-4 flex items-center gap-2">
                        <flux:icon name="user-plus" class="size-5 text-zinc-500" />
                        <flux:heading size="lg">{{ __('Add admin') }}</flux:heading>
                    </header>
                    <flux:separator class="mb-5" />

                    <form wire:submit="saveAdmin" class="space-y-4">
                        <flux:input wire:model="name" :label="__('Full name')" required autocomplete="name" />
                        <flux:input wire:model="email" type="email" :label="__('Email')" required autocomplete="email" />
                        <flux:input wire:model="password" type="password" :label="__('Password')" required autocomplete="new-password" viewable />
                        <flux:input wire:model="password_confirmation" type="password" :label="__('Confirm password')" required autocomplete="new-password" viewable />
                        <flux:button type="submit" variant="primary" icon="check" class="w-full" wire:loading.attr="disabled" wire:target="saveAdmin">
                            {{ __('Create admin user') }}
                        </flux:button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-xl border border-zinc-200/70 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="border-b border-zinc-200/70 p-4 dark:border-zinc-700/60">
                        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search admins')" class="sm:max-w-xs" />
                    </div>

                    <flux:table :paginate="$this->admins">
                        <flux:table.columns>
                            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">{{ __('Email') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('Added') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse ($this->admins as $adminUser)
                                <flux:table.row :key="'admin-'.$adminUser->id">
                                    <flux:table.cell>
                                        <div class="flex items-center gap-3">
                                            <flux:avatar size="sm" :name="$adminUser->name" />
                                            <flux:heading class="!text-sm">{{ $adminUser->name }}</flux:heading>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell class="text-zinc-600 dark:text-zinc-300">{{ $adminUser->email }}</flux:table.cell>
                                    <flux:table.cell class="whitespace-nowrap text-sm text-zinc-500">{{ $adminUser->created_at?->diffForHumans() }}</flux:table.cell>
                                    <flux:table.cell align="end">
                                        @if ($adminUser->id === auth()->id())
                                            <flux:badge size="sm" color="zinc" inset="top bottom">{{ __('You') }}</flux:badge>
                                        @else
                                            <flux:tooltip :content="__('Remove admin access')">
                                                <flux:button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="user-minus"
                                                    wire:click="revokeAdmin({{ $adminUser->id }})"
                                                    wire:confirm="{{ __('Remove dashboard access for this user? They keep their account.') }}"
                                                />
                                            </flux:tooltip>
                                        @endif
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="4">
                                        <div class="flex flex-col items-center gap-2 py-10 text-center">
                                            <flux:icon name="shield-check" class="size-8 text-zinc-400" />
                                            <flux:heading>{{ __('No admins found') }}</flux:heading>
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
