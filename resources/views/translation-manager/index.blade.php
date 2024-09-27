@extends('layouts.app')
@section('title')
    {{__('messages.placeholder.translation')}}
@endsection
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1>{{__('messages.placeholder.translation')}}</h1>
            <div class="section-header-breadcrumb">
                <button class="btn btn-primary form-btn addModal my-sm-0 my-1">
                    {{ __('messages.common.add') }} <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                        <form action="{{ route('translation-manager.update') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-xl-2 col-lg-3 col-md-4 col-12 mb-md-0 mb-3">
                                    <label for="lang">Selected Language:</label>
                                    <select id="lang" name="lang" class="form-control">
                                        @foreach($allLanguagesArr as $language)
                                            <option value="{{$language}}" @if($language == $selectedLang) selected @endif >{{ LANGUAGES[$language] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-4 col-12 mb-md-0 mb-3">
                                    <label for="file">Selected File:</label>
                                    <select id="file" name="file" class="form-control">
                                        @foreach($allFilesArr as $file)
                                            <option value="{{$file}}" @if($file == $selectedFile) selected @endif >{{ ucfirst($file) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-8 col-lg-6 col-md-4 col-12 mb-md-0 mb-3 text-right">
                                    <button type="submit" class="btn btn-primary form-btn">
                                        {{ __('messages.common.save') }}
                                    </button>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="selected-lang-messages">
                                @include('translation-manager.languages_form')
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @include('translation-manager.create')
    </section>
@endsection
@push('scripts')
<script>
    let translationUrl = '{{ route('translation-manager.index') }}';
</script>
<script src="{{mix('assets/js/language_translate/language_translate.js')}}"></script>
@endpush
