<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
        {{-- Mobile Specific Metas --}}
        <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">

        {{-- Favicons --}}
        <link href="{{ asset('logo.png') }}" rel="icon" sizes="32x32" type="image/png">
        <meta content="#ffffff" name="msapplication-TileColor">
        <meta content="#ffffff" name="theme-color">
    
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            {{-- image logo --}}
            <img src="{{ asset('logo.png') }}" alt="logo" class="size-12">
            {{-- <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" /> --}}
            {{-- <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate /> --}}
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                @if (auth()->user()?->is_admin)
                    <flux:separator class="my-2" />
                    <flux:sidebar.item icon="cube" :href="route('admin.products.index')"
                        :current="request()->routeIs('admin.products.*')" wire:navigate>
                        {{ __('Products') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="squares-2x2" :href="route('admin.categories.index')"
                        :current="request()->routeIs('admin.categories.*')" wire:navigate>
                        {{ __('Categories') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="receipt-percent" :href="route('admin.orders.index')"
                        :current="request()->routeIs('admin.orders.*')" wire:navigate>
                        {{ __('Orders') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('admin.inventory.index')"
                        :current="request()->routeIs('admin.inventory.*')" wire:navigate>
                        {{ __('Inventory') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="ticket" :href="route('admin.discounts.index')"
                        :current="request()->routeIs('admin.discounts.*')" wire:navigate>
                        {{ __('Discounts') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" :href="route('admin.users.index')"
                        :current="request()->routeIs('admin.users.*')" wire:navigate>
                        {{ __('Admin users') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('admin.customers.index')"
                        :current="request()->routeIs('admin.customers.*')" wire:navigate>
                        {{ __('Customers') }}
                    </flux:sidebar.item>
                @endif
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')"
                :current="request()->routeIs('profile.edit', 'security.edit', 'appearance.edit')" wire:navigate>
                {{ __('Settings') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        @auth
            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        @endauth
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            @auth
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
            @else
                <flux:profile icon-trailing="chevron-down" />
            @endauth

            <flux:menu>
                @auth
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer" data-test="logout-button">
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                @else
                    <flux:menu.item :href="route('login')" icon="arrow-right-end-on-rectangle" wire:navigate>
                        {{ __('Log in') }}
                    </flux:menu.item>
                @endauth
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>