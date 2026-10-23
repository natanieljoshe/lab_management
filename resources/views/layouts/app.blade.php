@extends('layouts.admin')

@section('title')
@yield('title', 'Academics Schedule - Sistem Penjadwalan Praktikum')
@endsection

@section('style')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
<style>
    [x-cloak] { display: none !important; }
</style>
@stack('styles')
@endsection

@section('body')
<div x-data="{
    toast: {
        show: false,
        message: '',
        type: 'info',
        timeout: null,
        notify(msg, type = 'info') {
            this.message = msg;
            this.type = type;
            this.show = true;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => { this.show = false; }, 4000);
        }
    }
}" class="relative w-full">
    <!-- Notification Toast -->
    <div x-cloak x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-5 right-5 z-[9999] max-w-sm w-full p-4 rounded-xl shadow-2xl flex items-start space-x-3 border"
         :class="{
             'bg-emerald-600 text-white border-emerald-500': toast.type === 'success',
             'bg-rose-600 text-white border-rose-500': toast.type === 'error',
             'bg-amber-600 text-white border-amber-500': toast.type === 'warning',
             'bg-slate-800 text-white border-slate-700': toast.type === 'info'
         }">
        <div class="flex-shrink-0 mt-0.5">
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </template>
            <template x-if="toast.type === 'warning'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </template>
            <template x-if="toast.type === 'info'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </template>
        </div>
        <div class="flex-1 text-sm font-medium leading-5" x-text="toast.message"></div>
        <button @click="toast.show = false" class="text-white/80 hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    @if(session('success'))
        <div x-init="toast.notify('{{ session('success') }}', 'success')"></div>
    @endif
    @if(session('error'))
        <div x-init="toast.notify('{{ session('error') }}', 'error')"></div>
    @endif
    @if(session('warning'))
        <div x-init="toast.notify('{{ session('warning') }}', 'warning')"></div>
    @endif

    <!-- Content Area -->
    <div class="w-full">
        @yield('content')
    </div>
</div>
@endsection

@section('script')
@stack('scripts')
@endsection
