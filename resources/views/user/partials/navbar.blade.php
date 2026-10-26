<nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200" id="navbar">
    <div class="relative container mx-auto px-6 py-3 flex justify-between items-center">
        <a href="/" class="flex items-center gap-2 group">
            {{-- Ganti dengan logo UK Petra asli jika ada --}}
            <div class="w-10 h-10 bg-petra-blue rounded-md flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-building-columns text-petra-yellow text-xl"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold text-petra-blue leading-tight tracking-wide">
                    LABORATORIUM
                </span>
                <span class="text-xs text-petra-darkgray font-medium tracking-wider">
                    GEDUNG P - UK PETRA
                </span>
            </div>
        </a>

        <div class="hidden md:flex items-center space-x-8 font-medium text-sm">
            <a href="{{ route('user.landing') }}#about" class="text-petra-darkgray hover:text-petra-blue transition-colors relative after:absolute after:bottom-[-5px] after:left-0 after:h-[2px] after:w-0 after:bg-petra-yellow after:transition-all hover:after:w-full">TENTANG</a>
            <a href="{{ route('user.landing') }}#services" class="text-petra-darkgray hover:text-petra-blue transition-colors relative after:absolute after:bottom-[-5px] after:left-0 after:h-[2px] after:w-0 after:bg-petra-yellow after:transition-all hover:after:w-full">LAYANAN</a>

            @guest
            <a href="{{ route('user.login') }}"
                class="px-5 py-2 rounded text-sm font-bold bg-petra-blue text-white hover:bg-petra-yellow hover:text-petra-blue transition-all duration-300 shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> LOGIN
            </a>
            @endguest

            @auth
            <div class="relative group">
                <button class="flex items-center gap-2 text-petra-darkgray hover:text-petra-blue transition-colors font-bold py-2">
                    <span>Halo, {{ explode(' ', Auth::user()->name)[0] }}</span> {{-- Ambil nama depan saja --}}
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                </button>

                <div class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 z-50">
                    <div class="py-1">
                        <div class="px-4 py-2 border-b border-gray-100 bg-gray-50">
                            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Menu Mahasiswa</span>
                        </div>

                        <a href="{{ route('user.booking.form') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-petra-blue transition-colors">
                            <i class="fa-regular fa-calendar-plus w-5 mr-2 text-center text-petra-blue"></i>Buat Peminjaman
                        </a>

                        <a href="{{ route('user.booking.history') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-petra-blue transition-colors">
                            <i class="fa-solid fa-clock-rotate-left w-5 mr-2 text-center text-petra-blue"></i>Riwayat Peminjaman
                        </a>

                        <div class="border-t border-gray-100"></div>

                        <a href="{{ route('user.helpdesk.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-petra-blue transition-colors">
                            <i class="fa-solid fa-headset w-5 mr-2 text-center text-petra-blue"></i>Pusat Bantuan / Lapor
                        </a>

                        <a href="{{ route('user.helpdesk.history') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-petra-blue transition-colors">
                            <i class="fa-solid fa-clipboard-list w-5 mr-2 text-center text-petra-blue"></i>Riwayat Tiket Saya
                        </a>

                        <div class="border-t border-gray-100 mt-1"></div>

                        <a href="{{ route('user.logout') }}" class="block px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket w-5 mr-2 text-center"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
            @endauth
        </div>

        <button class="md:hidden text-petra-blue text-2xl focus:outline-none transition-transform active:scale-95" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div id="mobile-menu" class="hidden md:hidden relative bg-white border-b border-gray-200 shadow-lg animate-fade-in-down">
        <div class="flex flex-col p-4 space-y-3 font-medium">
            <a href="{{ route('user.landing') }}#about" class="text-petra-darkgray hover:text-petra-blue py-2 border-b border-gray-50">TENTANG</a>
            <a href="{{ route('user.landing') }}#services" class="text-petra-darkgray hover:text-petra-blue py-2 border-b border-gray-50">LAYANAN</a>
            <a href="{{ route('user.helpdesk.index') }}" class="text-petra-darkgray hover:text-petra-blue py-2 border-b border-gray-50">HELPDESK</a>

            @guest
            <a href="{{ route('user.login') }}" class="mt-2 w-full text-center px-5 py-3 rounded text-sm font-bold bg-petra-blue text-white hover:bg-petra-yellow hover:text-petra-blue transition-all shadow-sm">
                LOGIN SEKARANG
            </a>
            @endguest

            @auth
            <div class="mt-2 pt-2 border-t border-gray-200">
                <p class="text-xs text-gray-400 uppercase font-bold mb-2">Halo, {{ Auth::user()->name }}</p>
                <a href="{{ route('user.booking.form') }}" class="flex items-center text-petra-darkgray hover:text-petra-blue py-2">
                    <i class="fa-regular fa-calendar-plus w-6"></i> Buat Peminjaman
                </a>
                <a href="{{ route('user.booking.history') }}" class="flex items-center text-petra-darkgray hover:text-petra-blue py-2">
                    <i class="fa-solid fa-clock-rotate-left w-6"></i> Riwayat Peminjaman
                </a>
                <a href="{{ route('user.helpdesk.index') }}" class="flex items-center text-petra-darkgray hover:text-petra-blue py-2">
                    <i class="fa-solid fa-headset w-6"></i> Pusat Bantuan / Lapor
                </a>
                <a href="{{ route('user.helpdesk.history') }}" class="flex items-center text-petra-darkgray hover:text-petra-blue py-2">
                    <i class="fa-solid fa-clipboard-list w-6"></i> Riwayat Tiket Saya
                </a>
                <a href="{{ route('user.logout') }}" class="flex items-center text-red-500 py-2 mt-2">
                    <i class="fa-solid fa-arrow-right-from-bracket w-6"></i> Logout
                </a>
            </div>
            @endauth
        </div>
    </div>
</nav>