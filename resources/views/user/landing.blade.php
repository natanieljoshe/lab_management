@extends('layouts.user')

@section('title', 'Selamat Datang di Laboratorium Gedung P')

@section('body')
    
    @include('user.partials.navbar')

    <main>
        
        {{-- HERO SECTION --}}
        <section class="relative h-[90vh] flex items-center justify-center bg-petra-gray overflow-hidden mt-16">
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=2000&auto=format&fit=crop" 
                     alt="Suasana Laboratorium Terang" 
                     class="w-full h-full object-cover opacity-20">
                <div class="absolute inset-0 bg-gradient-to-r from-petra-gray via-petra-gray/80 to-transparent"></div>
            </div>

            <div class="container mx-auto px-6 relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <div class="flex gap-2 mb-6">
                        <div class="h-2 w-16 bg-petra-yellow rounded-full"></div>
                        <div class="h-2 w-8 bg-petra-wood rounded-full"></div>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-petra-blue mb-6 leading-tight">
                        Pusat Inovasi & <br>Praktikum <span class="text-petra-yellow">Gedung P</span>
                    </h1>
                    
                    <p class="text-lg text-petra-darkgray mb-8 leading-relaxed max-w-xl">
                        Fasilitas laboratorium terpadu Universitas Kristen Petra untuk mendukung pembelajaran, penelitian, dan pengembangan kompetensi mahasiswa dalam lingkungan yang profesional dan modern.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('user.booking.form') }}" 
                           class="px-8 py-3 bg-petra-blue text-white font-bold rounded shadow-md hover:bg-petra-yellow hover:text-petra-blue hover:shadow-lg transition-all duration-300 flex items-center gap-3">
                           <i class="fa-solid fa-calendar-days"></i> RESERVASI SEKARANG
                        </a>
                        <a href="#about" 
                           class="px-8 py-3 bg-white text-petra-blue font-bold rounded border-2 border-petra-blue hover:bg-petra-blue/10 transition-all duration-300">
                           Pelajari Fasilitas
                        </a>
                    </div>
                </div>
                 <div></div>
            </div>
        </section>

        {{-- ABOUT SECTION --}}
        <section id="about" class="py-24 bg-white relative">
             <div class="absolute top-0 left-0 w-2 h-full bg-petra-wood opacity-50"></div>

            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                     <div class="relative" data-aos="fade-right">
                        <div class="absolute -bottom-4 -left-4 w-full h-full border-4 border-petra-wood/30 rounded-lg -z-10"></div>
                        <div class="rounded-lg overflow-hidden shadow-xl">
                            <img src="https://placehold.co/800x600/f1f5f9/003366?text=Aktivitas+Praktikum+Mahasiswa" 
                                 alt="Aktivitas Lab" 
                                 class="w-full object-cover hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>

                    <div data-aos="fade-left">
                        <h2 class="text-3xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                            <i class="fa-solid fa-circle-info text-petra-yellow"></i>
                            Tentang Fasilitas Kami
                        </h2>
                        <p class="text-petra-darkgray mb-6 text-justify leading-7 text-lg">
                            Laboratorium di Gedung P UK Petra dirancang untuk memenuhi standar akademik dan industri terkini. Kami menyediakan lingkungan yang kondusif bagi mahasiswa untuk bereksperimen dan mengaplikasikan teori yang dipelajari di kelas.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-6 mt-8">
                            <div class="bg-petra-gray p-4 rounded-lg border-l-4 border-petra-blue shadow-sm">
                                <i class="fa-solid fa-desktop text-2xl text-petra-blue mb-2"></i>
                                <h3 class="font-bold text-lg mb-1">Hardware Terkini</h3>
                                <p class="text-sm text-gray-600">Perangkat komputasi spesifikasi tinggi untuk menunjang software modern.</p>
                            </div>
                            <div class="bg-petra-gray p-4 rounded-lg border-l-4 border-petra-yellow shadow-sm">
                                <i class="fa-solid fa-screwdriver-wrench text-2xl text-petra-yellow mb-2"></i>
                                <h3 class="font-bold text-lg mb-1">Peralatan Lengkap</h3>
                                <p class="text-sm text-gray-600">Ketersediaan alat ukur dan komponen elektronik untuk tugas akhir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- SERVICES & FACILITIES (2 Cards: Lab & Items) --}}
        <section id="services" class="py-24 bg-petra-gray relative">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16" data-aos="fade-up">
                    <h2 class="text-3xl md:text-4xl font-bold text-petra-blue mb-4 inline-block relative relative after:absolute after:bottom-[-10px] after:left-1/2 after:-translate-x-1/2 after:h-[3px] after:w-24 after:bg-petra-yellow">
                        Layanan & Peminjaman
                    </h2>
                    <p class="text-petra-darkgray max-w-xl mx-auto mt-6 text-lg">
                        Akses mudah untuk reservasi ruangan dan peminjaman inventaris melalui sistem terintegrasi kami.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    
                    <div class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border-t-4 border-petra-blue" data-aos="fade-up" data-aos-delay="100">
                        <div class="h-56 overflow-hidden relative">
                             <img src="https://placehold.co/600x400/e2e8f0/003366?text=Ruang+Laboratorium+Komputer" 
                                 alt="Computer Lab Room" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-2xl font-bold text-petra-blue">Laboratorium</h3>
                                <i class="fa-solid fa-building-columns text-4xl text-petra-blue/20 group-hover:text-petra-blue transition-colors"></i>
                            </div>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Reservasi ruangan laboratorium komputer untuk keperluan praktikum mandiri, pengerjaan tugas besar, atau penelitian.
                            </p>
                            <ul class="text-sm text-gray-500 space-y-2 mb-8">
                                <li><i class="fa-solid fa-check-circle text-petra-blue mr-2"></i>PC Spesifikasi Tinggi</li>
                                <li><i class="fa-solid fa-check-circle text-petra-blue mr-2"></i>Koneksi Internet Cepat</li>
                                <li><i class="fa-solid fa-check-circle text-petra-blue mr-2"></i>Ruangan Ber-AC</li>
                            </ul>
                            <a href="{{ route('user.booking.form') }}" class="block w-full text-center py-3 rounded bg-petra-gray text-petra-blue font-bold border border-petra-blue hover:bg-petra-blue hover:text-white transition-all">
                                BOOKING RUANGAN
                            </a>
                        </div>
                    </div>

                    <div class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border-t-4 border-petra-yellow" data-aos="fade-up" data-aos-delay="200">
                        <div class="h-56 overflow-hidden relative">
                            <img src="https://placehold.co/600x400/fffbeb/b45309?text=Inventaris+Alat+Elektronik" 
                                 alt="Electronic Items" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="p-8">
                             <div class="flex items-center justify-between mb-4">
                                <h3 class="text-2xl font-bold text-petra-blue">Inventaris Alat</h3>
                                <i class="fa-solid fa-boxes-stacked text-4xl text-petra-yellow/30 group-hover:text-petra-yellow transition-colors"></i>
                            </div>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Peminjaman berbagai macam peralatan elektronik, kit development (IoT), dan alat ukur untuk mendukung proyek Anda.
                            </p>
                            <ul class="text-sm text-gray-500 space-y-2 mb-8">
                                <li><i class="fa-solid fa-check-circle text-petra-yellow mr-2"></i>Microcontroller & Sensor</li>
                                <li><i class="fa-solid fa-check-circle text-petra-yellow mr-2"></i>Alat Ukur (Oscilloscope, dll)</li>
                                <li><i class="fa-solid fa-check-circle text-petra-yellow mr-2"></i>Perangkat Jaringan</li>
                            </ul>
                            <a href="{{ route('user.booking.form') }}" class="block w-full text-center py-3 rounded bg-petra-gray text-petra-blue font-bold border border-petra-yellow hover:bg-petra-yellow hover:text-petra-blue transition-all">
                                PINJAM ALAT
                            </a>
                        </div>
                    </div>

                    <div class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border-t-4 border-indigo-600" data-aos="fade-up" data-aos-delay="300">
                        <div class="h-56 overflow-hidden relative">
                             <img src="https://placehold.co/600x400/e0e7ff/3730a3?text=Helpdesk+%26+Layanan+Lab" 
                                 alt="Helpdesk & Pengaduan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="p-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-2xl font-bold text-petra-blue">Helpdesk & Saran</h3>
                                <i class="fa-solid fa-headset text-4xl text-indigo-500/30 group-hover:text-indigo-600 transition-colors"></i>
                            </div>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Pusat pengaduan kerusakan perangkat lab serta sarana penyampaian keluhan dan saran untuk peningkatan mutu lab.
                            </p>
                            <ul class="text-sm text-gray-500 space-y-2 mb-8">
                                <li><i class="fa-solid fa-check-circle text-indigo-600 mr-2"></i>Lapor Kerusakan PC & Meja</li>
                                <li><i class="fa-solid fa-check-circle text-indigo-600 mr-2"></i>Keluhan Fasilitas Ruang Lab</li>
                                <li><i class="fa-solid fa-check-circle text-indigo-600 mr-2"></i>Tracking Status & Tanggapan</li>
                            </ul>
                            <a href="{{ route('user.helpdesk.index') }}" class="block w-full text-center py-3 rounded bg-petra-gray text-petra-blue font-bold border border-indigo-600 hover:bg-indigo-600 hover:text-white transition-all">
                                PUSAT BANTUAN
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- CALL TO ACTION (CTA) --}}
        <section class="py-20 relative bg-petra-wood/20">
            <div class="container mx-auto px-6 relative z-10 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-petra-blue mb-6" data-aos="zoom-in-up">
                    Permudah Jadwal Praktikum & Riset Anda
                </h2>
                <p class="text-petra-darkgray text-lg mb-10 max-w-2xl mx-auto leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                    Sistem booking kami memastikan Anda mendapatkan fasilitas yang dibutuhkan tepat waktu. Cek ketersediaan secara real-time sekarang.
                </p>
                
                <div data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('user.booking.form') }}" 
                       class="inline-flex items-center gap-3 px-10 py-4 bg-petra-blue text-white font-bold text-lg rounded shadow-lg hover:bg-petra-yellow hover:text-petra-blue hover:scale-105 transition-all duration-300">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        MULAI RESERVASI
                    </a>
                </div>
            </div>
        </section>

    </main>

    @include('user.partials.footer')

@endsection

@section('scripts')
<script>
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('shadow-md', 'py-2');
            navbar.classList.remove('shadow-sm', 'py-3');
        } else {
            navbar.classList.add('shadow-sm', 'py-3');
            navbar.classList.remove('shadow-md', 'py-2');
        }
    });

    if (window.location.hash) {
        setTimeout(() => {
            const target = document.querySelector(window.location.hash);
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
</script>
@endsection