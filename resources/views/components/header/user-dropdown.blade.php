@auth

    @php
        $user = auth()->user();

        // Menu items with clean SVG icons
        $menuItems = [
            [
                'text' => 'Edit Profile',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                       </svg>',
                'route' => route('users.show', auth()->user()),
            ],
            [
                'text' => 'Change Password',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                       </svg>',
                'route' => route('password.edit', auth()->user()),
            ],
        ];
    @endphp

    <!-- Keep the rest of your HTML exactly as is -->
    <!-- ... -->

    <div class="relative" x-data="{
        dropdownOpen: false,
        toggleDropdown() { this.dropdownOpen = !this.dropdownOpen },
        closeDropdown() { this.dropdownOpen = false }
    }" @click.away="closeDropdown()">

        <!-- User Button -->
        <button class="flex items-center text-gray-700 dark:text-gray-400" @click.prevent="toggleDropdown()" type="button">
            <span class="mr-3 overflow-hidden rounded-full h-11 w-11">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="object-cover w-full h-full" />
            </span>
            <span class="block mr-1 font-medium text-theme-sm">{{ $user->full_name }}</span>
            <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Dropdown -->
        <div x-show="dropdownOpen" x-transition
            class="absolute right-0 mt-[17px] w-[260px] rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
            style="display: none;">

            <!-- User Info -->
            <div class="px-2">
                <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-300">{{ $user->full_name }}</span>
                <span class="block mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
            </div>

            <!-- Menu Items -->
            <ul class="flex flex-col gap-1 pt-4 pb-3 mt-3 border-t border-gray-200 dark:border-gray-800">
                @foreach ($menuItems as $item)
                    <li>
                        <a href="{{ $item['route'] }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-theme-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-white/5 dark:text-gray-400"
                            @click="closeDropdown()">
                            <span class="text-gray-500">{!! $item['icon'] !!}</span>
                            {{ $item['text'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                    class="flex items-center w-full gap-3 px-3 py-2 font-medium rounded-lg text-theme-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-white/5 dark:text-gray-400"
                    @click="closeDropdown()">
                    <span class="text-gray-500">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    Sign out
                </button>
            </form>

        </div>
    </div>
@endauth
