<div id="createCustomerModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content left-margin">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.customer.add_customer') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id' => 'createCustomer', 'files'=>true, 'autocomplete' => 'off']) }}
            <div class="modal-body">
                {{ Form::hidden('role', getCustomerRoleId()) }}
                {{ csrf_field() }}
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('name', __('messages.common.name').':') }}<span
                                class="text-danger">*</span>
                        {{ Form::text('name', null, ['id'=>'customerFirstName','class' => 'form-control','required']) }}
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('email',__('messages.common.email').':') }}<span class="text-danger">*</span>
                        {{ Form::email('email', null, ['id'=>'customerEmail','class' => 'form-control','required']) }}
                    </div>

                    <div class="form-group col-sm-6">
                        {{ Form::label('password', __('messages.common.password').':') }}<span
                                class="text-danger">*</span>
                        <div class="input-group">
                            <input name="password" type="password" id="password"
                                   class="form-control">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <a href="javascript:void(0)" class="" onclick="showPassword('password')">
                                        <i class="fa fa-eye-slash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('password_confirmation',  __('messages.common.confirm_password').':') }}<span
                                class="text-danger">*</span>
                        <div class="input-group">
                            <input name="password_confirmation" type="password" id="confirmPassword"
                                   class="form-control" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <a href="javascript:void(0)" class="" onclick="showPassword('confirmPassword')">
                                        <i class="fa fa-eye-slash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
                    <button type="button" class="btn btn-light left-margin"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
