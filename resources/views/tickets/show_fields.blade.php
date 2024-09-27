<div class="row details-page">
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('name', __('messages.common.first_name').':') }}
        <p>{{ $ticket->title }}</p>
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('email', __('messages.common.email').':') }}
        <p>{{ $ticket->email }}</p>
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('category', __('messages.category.category').':') }}
        <p>{{ $ticket->category->name }}</p>
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('subject', __('messages.ticket.subject').':') }}
        <p>{{ !empty($ticket->subject) ? $ticket->subject:__('messages.common.n/a') }}</p>
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('description', __('messages.common.description').':') }}
        <p>{!!  !empty($ticket->description) ? $ticket->description:__('messages.common.n/a')!!}  </p>
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('created_on', __('messages.common.created_on').':') }}
        <p>{{ date('jS M, Y', strtotime($ticket->created_at))  }}</p>
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('created_on', __('messages.common.created_by').':') }}
        <p>{{ $ticket->user->name  }}</p>
    </div>
</div>
