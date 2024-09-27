@extends('web.app')
@section('title')
    {{ __('messages.faq.faqs') }}
@endsection
@push('css')
    @livewireStyles
@endpush
@section('content')
    @livewire('faqfront')
@endsection
@push('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
@endpush
