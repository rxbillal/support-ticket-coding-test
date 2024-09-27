<div class="row">
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('title', __('messages.ticket.ticket_title').':') }}<span class="text-danger">*</span>
        {{ Form::text('title', null, ['class' => 'form-control','required','autofocus'=>'true']) }}
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('email', __('messages.common.email').':') }}<span class="text-danger">*</span>
        {{ Form::email('email', null, ['class' => 'form-control', 'required','id'=>'email']) }}
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('category_id', __('messages.category.category').':') }}<span class="text-danger">*</span>
        <div class="input-group">
            {{ Form::select('category_id', $data['categories'] ,null, ['id'=>'categoryId','class' => 'form-control','placeholder' => __('messages.admin_dashboard.select_category'), 'required']) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" class="addCategoryModal" data-target="#addModal">
                        <i class="fa fa-plus" data-toggle="tooltip" data-placement="top" 
                           title="{{ __('messages.category.add_category') }}">
                        </i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('assignTo', __('messages.agent.agents').':') }}<span class="text-danger">*</span>
        {{ Form::select('assignTo[]', $data['users'] ,null, ['id'=>'assignedTo','class' => 'form-control dropup','required','multiple' => 'multiple']) }}
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        {{ Form::label('customer', __('messages.customer.customer').':') }}<span class="text-danger">*</span>
        <div class="input-group">
            {{ Form::select('customer', $data['customers'] ,null, ['id'=>'customer','class' => 'form-control', 'required', 'placeholder' => __('messages.user.select_customer')]) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" class="addCustomerModal" data-target="#createCustomerModal">
                        <i class="fa fa-plus" data-toggle="tooltip" data-placement="top"
                           title="{{ __('messages.customer.add_customer') }}">
                        </i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-xl-6 col-md-6 col-sm-12">
        <div class="selectgroup selectgroup-pills">
            {{ Form::label('is_public', __('messages.ticket.ticket_type').':') }}
            <div class="row ticket-type-add">
                <label class="selectgroup-item mb-0 ml-3 public-ticket-select-w">
                    <input type="radio" name="is_public" value="1" class="selectgroup-input">
                    <span
                            class="selectgroup-button "><span><i class="fas fa-users"></i>&nbsp;</span>{{ __('messages.ticket.is_public') }}</span>
                </label>
                <label class="selectgroup-item mb-0 private-ticket-add public-ticket-select-w">
                    <input type="radio" name="is_public" value="0" class="selectgroup-input" checked="">
                    <span
                            class="selectgroup-button custom-ticket-new"><span><i class="fas fa-lock"></i>&nbsp;</span>{{ __('messages.ticket.is_private') }}</span>
                </label>
            </div>
        </div>
    </div>
    <div class="form-group col-xl-12 col-md-12 col-sm-12">
        {{ Form::label('attachment', __('messages.ticket.attachments').':') }}
        <span><span id="attachment-counter">0</span> {{ strtolower(__('messages.ticket.attachments')) }}</span>
        <div class="d-flex">
            <a href="javascript:void(0)" id="attachmentButton" class="btn btn-primary px-8"
               data-toggle="modal" data-target="#addAttachment">
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
        <a href="{{ route('ticket.index') }}" class="btn btn-secondary text-dark">{{__('messages.common.cancel')}}</a>
    </div>

</div>
