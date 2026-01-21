@auth
@php
    $user = auth()->user();

    // Menu items with dynamic SVG icons
    $menuItems = [
        [
            'text' => 'Edit Profile',
            'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566Z"
                              fill="currentColor"/>
                       </svg>',
            'route' => route('profile.show'),
        ],
        [
            'text' => 'Account Settings',
            'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M10.4858 3.5L13.5182 3.5C13.9233 3.5 14.2518 3.82851 14.2518 4.23377C14.2518 5.9529 16.1129 7.02795 17.602 6.1682C17.9528 5.96567 18.4014 6.08586 18.6039 6.43667L20.1203 9.0631C20.3229 9.41407 20.2027 9.86286 19.8517 10.0655C18.3625 10.9253 18.3625 13.0747 19.8517 13.9345C20.2026 14.1372 20.3229 14.5859 20.1203 14.9369L18.6039 17.5634C18.4013 17.9142 17.9528 18.0344 17.602 17.8318C16.1129 16.9721 14.2518 18.0471 14.2518 19.7663C14.2518 20.1715 13.9233 20.5 13.5182 20.5H10.4858C10.0804 20.5 9.75182 20.1714 9.75182 19.766C9.75182 18.0461 7.88983 16.9717 6.40067 17.8314C6.04945 18.0342 5.60037 17.9139 5.39767 17.5628L3.88167 14.937C3.67903 14.586 3.79928 14.1372 4.15026 13.9346C5.63949 13.0748 5.63946 10.9253 4.15025 10.0655C3.79926 9.86282 3.67901 9.41401 3.88165 9.06303L5.39764 6.43725C5.60034 6.08617 6.04943 5.96581 6.40065 6.16858C7.88982 7.02836 9.75182 5.9539 9.75182 4.23399C9.75182 3.82862 10.0804 3.5 10.4858 3.5Z"
                              fill="currentColor"/>
                       </svg>',
            'route' => route('profile.show'),
        ],
        [
            'text' => 'Support',
            'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M3.5 12C3.5 7.30558 7.30558 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C7.30558 20.5 3.5 16.6944 3.5 12Z"
                              fill="currentColor"/>
                       </svg>',
            'route' => route('profile.show'),
        ],
    ];
@endphp

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
        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown -->
    <div x-show="dropdownOpen"
         x-transition
         class="absolute right-0 mt-[17px] w-[260px] rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
         style="display: none;">

        <!-- User Info -->
        <div class="px-2">
            <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-300">{{ $user->full_name }}</span>
            <span class="block mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
        </div>

        <!-- Menu Items -->
        <ul class="flex flex-col gap-1 pt-4 pb-3 mt-3 border-t border-gray-200 dark:border-gray-800">
            @foreach($menuItems as $item)
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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                Sign out
            </button>
        </form>

    </div>
</div>
@endauth
