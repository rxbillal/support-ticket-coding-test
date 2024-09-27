<div class="row">
    <input type="hidden" name="deletedMediaId">
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('title', __('messages.ticket.ticket_title')) }}<span class="text-danger">*</span>
        {{ Form::text('title', null, ['class' => 'form-control','required','autofocus'=>'true']) }}
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('email', __('messages.common.email').':') }}<span class="text-danger">*</span>
        {{ Form::email('email', null, ['class' => 'form-control', 'required','id'=>'editEmail', 'readonly' => 'true']) }}
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('category_id', __('messages.category.category').':') }}<span class="text-danger">*</span>
        {{ Form::select('category_id', $data['categories'] ,null, ['id'=>'categoryId','class' => 'form-control','placeholder' => __('messages.admin_dashboard.select_category'),'required']) }}
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        <div class="selectgroup selectgroup-pills">
            {{ Form::label('is_public', __('messages.ticket.ticket_type').':') }}
            <div class="row ticket-type-edit">
                <label class="selectgroup-item mb-0 ml-3 col-lg-4 col-md-5 col-sm-12 col-12">
                    <input type="radio" name="is_public" value="1" class="selectgroup-input"
                           @if($ticket->is_public == 1) checked @endif>
                    <span
                            class="selectgroup-button">{{ __('messages.ticket.is_public') }}</span>
                </label>
                <label class="selectgroup-item mb-0 private-ticket-edit col-lg-4 col-md-5 col-sm-12 col-12">
                    <input type="radio" name="is_public" value="0" class="selectgroup-input"
                           @if($ticket->is_public == 0) checked @endif>
                    <span
                            class="selectgroup-button">{{ __('messages.ticket.is_private') }}</span>
                </label>
            </div>
        </div>
    </div>
    <div class="form-group col-xl-12 col-md-12 col-sm-12">
        {{ Form::label('attachments', __('messages.ticket.attachments').':') }}
        <span><span id="attachment-counter">0</span> {{ strtolower(__('messages.ticket.attachments')) }}</span>
        <div class="d-flex">
            <a href="javascript:void(0)" id="editAttachmentButton" class="btn btn-primary px-8"
               data-toggle="modal" data-target="#editAttachment">
                {{ __('messages.ticket.attachments') }}
            </a>
        </div>
    </div>
    <div class="form-group col-xl-12 col-md-12 col-sm-12">
        {{ Form::label('description', __('messages.common.description').':') }}<span class="text-danger">*</span>
        {{ Form::textarea('description', null, ['class' => 'form-control' , 'id' => 'details', 'rows' => '5']) }}
    </div>

    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
        <a href="{{ route('customer.myTicket') }}"
           class="btn btn-secondary text-dark">{{__('messages.common.cancel')}}</a>
    </div>

</div>
