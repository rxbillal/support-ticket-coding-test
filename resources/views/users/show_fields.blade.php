@php
    /** @var \App\Models\User $user */
@endphp
<div class="row details-page mb-2">
    <div class="col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('name', __('messages.common.name').':') }}
        <p>{{ $user->name }}</p>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('email', __('messages.common.email').':') }}
        <p>{{ $user->email }}</p>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('phone', __('messages.common.phone').':') }}
        <p>{{ !empty($user->phone) ? $user->phone:__('messages.common.n/a') }}</p>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('gender', __('messages.user.gender').':') }}
        <p>{{ !empty($user->gender) ? ($user->gender == \App\Models\User::MALE) ? 'Male' : 'Female'  :__('messages.common.n/a') }}</p>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
        {{ Form::label('created_on', __('messages.common.created_on').':') }}
        <p>{{ Illuminate\Support\Carbon::parse($user->created_at)->isoFormat('Do MMMM, YYYY')}}</p>
    </div>
    <div class="col-xl-4 col-md-4 col-sm-12">
        {{ Form::label('image', __('messages.user.profile').':') }}
        <img src="{{$user->photo_url}}" alt="No Image" class="thumbnail-preview ml-3"/>
    </div>
    <div class="col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('about', __('messages.user.about').':') }}
        <div class="user-about-us">
            <p>{!! !empty($user->about) ? $user->about : __('messages.common.n/a') !!}</p>
        </div>
    </div>
</div>
