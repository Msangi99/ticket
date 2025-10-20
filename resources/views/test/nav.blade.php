<nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md shadow-sm">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img src="{{ asset('ChatGPT Image Jul 7, 2025, 12_18_13 PM.png') }}" alt="Bus icon"
                class="h-10 w-10 rounded-2xl" loading="lazy">
            <a href="{{ route('home') }}" wire:navigate
                class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-pink-600 bg-clip-text text-transparent">Highlink ISGC</a>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-6">
            <a wire:navigate href="{{ route('home') }}"
                class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200">Home</a>
            <a wire:navigate href="{{ route('about') }}"
                class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200">About Us</a>
            <a wire:navigate href="{{ route('contact') }}"
                class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200">Contact Us</a>
            <a wire:navigate href="{{ route('info') }}"
                class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200">Booking Info</a>
            <a href="tel:+255755879793"
                class="flex items-center text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200">
                <img src="{{ asset('images/phone-call.png') }}" alt="Phone icon" class="mr-2 h-5 w-5" loading="lazy">
                <span>+255 755 879 793</span>
            </a>
        </div>

        <!-- Auth Actions -->
        <div class="flex items-center space-x-4">
            @guest
                <a wire:navigate href="{{ route('login') }}"
                    class="hidden md:block px-4 py-2 text-indigo-600 border border-indigo-600 rounded-md hover:bg-indigo-50 transition-colors duration-200">
                    Sign In
                </a>
                <a wire:navigate href="{{ route('register') }}"
                    class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:bg-opacity-90 transition-colors duration-200 shadow-sm">
                    Register
                </a>
            @endguest
            @auth
                @if (auth()->user()->isAdmin())
                    <a wire:navigate href="{{ route('system.index') }}"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:bg-opacity-90 transition-colors duration-200 shadow-sm">
                        Dashboard
                    </a>
                @elseif (auth()->user()->isBuscampany())
                    <a wire:navigate href="{{ route('index') }}"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:bg-opacity-90 transition-colors duration-200 shadow-sm">
                        Dashboard
                    </a>
                @elseif (auth()->user()->isVender())
                    <a wire:navigate href="{{ route('vender.index') }}"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:bg-opacity-90 transition-colors duration-200 shadow-sm">
                        Dashboard
                    </a>
                @elseif (auth()->user()->role == 'customer')
                    <a wire:navigate href="{{ route('customer.index') }}"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:bg-opacity-90 transition-colors duration-200 shadow-sm">
                        Dashboard
                    </a>
                @endif

                <button id="mobile-menu-btn" class="md:hidden text-gray-700 focus:outline-none"
                    aria-label="Toggle mobile menu">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            @endauth
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="md:hidden hidden fixed top-14 left-0 right-0 bg-white/90 backdrop-blur-md shadow-lg z-40">
        <div class="flex flex-col space-y-1 p-4">
            <a wire:navigate href="{{ route('home') }}"
                class="px-4 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors duration-200">Home</a>
            <a wire:navigate href="{{ route('about') }}"
                class="px-4 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors duration-200">About
                Us</a>
            <a wire:navigate href="{{ route('contact') }}"
                class="px-4 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors duration-200">Contact
                Us</a>
            <a wire:navigate href="{{ route('info') }}"
                class="px-4 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors duration-200">Booking
                Info</a>
            <a href="tel:+255755879793"
                class="px-4 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-colors duration-200 flex items-center">
                <img src="{{ asset('images/phone-call.png') }}" alt="Phone icon" class="mr-2 h-5 w-5" loading="lazy">
                <span>+255 555 879793</span>
            </a>
            @guest
                <div class="pt-2 border-t border-gray-200">
                    <a wire:navigate href="{{ route('login') }}"
                        class="block w-full px-4 py-2 text-indigo-600 border border-indigo-600 rounded-md hover:bg-indigo-50 transition-colors duration-200 mb-2">
                        Sign In
                    </a>
                    <a wire:navigate href="{{ route('register') }}"
                        class="block w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:bg-opacity-90 transition-colors duration-200">
                        Register
                    </a>
                </div>
            @endguest
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn').addEventListener('click', () => {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
