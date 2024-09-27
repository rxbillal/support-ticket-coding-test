@extends('layouts.app')
@section('title')
    {{__('messages.admin.admin') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3">{{ __('messages.admin.edit_admin') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('admins.index')  }}"
                   class="btn btn-primary form-btn float-right my-sm-0 my-1">{{__('messages.common.back')}}</a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">
                    {{ Form::model($admin, ['route' => ['admins.update', $admin->id], 'method' => 'put', 'files' => 'true', 'id' => 'editCompanyForm', 'autocomplete' => 'off']) }}

                    @include('admins.edit_fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
@endsection
<script>

</script>
@push('scripts')
    <script>
        let isEdit = true
        let phoneNo = "{{ old('region_code').old('phone') }}"
    </script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{mix('assets/js/users/create_edit.js')}}"></script>
@endpush
