@extends('settings.index')
@section('title')
    {{ __('messages.social_settings') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update','id'=>'editForm', 'autocomplete' => 'off']) }}
    <div class="row mt-3">
        <div class="form-group col-sm-6">
            {{ Form::label('facebook_url', __('messages.setting.facebook_url').':') }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fab fa-facebook-f facebook-fa-icon"></i>
                    </div>
                </div>
                {{ Form::text('facebook_url', $setting['facebook_url'], ['class' => 'form-control','id'=>'facebookUrl']) }}
            </div>
        </div>
        <div class="form-group col-sm-6">
            {{ Form::label('twitter_url', __('messages.setting.twitter_url').':') }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fab fa-twitter twitter-fa-icon"></i>
                    </div>
                </div>
                {{ Form::text('twitter_url', $setting['twitter_url'], ['class' => 'form-control','id'=>'twitterUrl']) }}
            </div>
        </div>
        <div class="form-group col-sm-6">
            {{ Form::label('linkedIn_url', __('messages.setting.linkedIn_url').':') }}
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fab fa-linkedin-in linkedin-fa-icon"></i>
                    </div>
                </div>
                {{ Form::text('linkedIn_url', $setting['linkedIn_url'], ['class' => 'form-control','id'=>'linkedInUrl']) }}
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <!-- Submit Field -->
        <div class="form-group col-sm-12">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id'=>'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
            {{ Form::reset(__('messages.common.cancel'), ['class' => 'btn btn-light text-dark','id'=>'btn-reset']) }}
        </div>
    </div>
    {{ Form::close() }}
@endsection
