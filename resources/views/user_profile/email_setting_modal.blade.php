<div id="changeEmailSetting" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content left-margin">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.email_setting.email_setting') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
                {{ csrf_field() }}
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="emailSettingMessageBox"></div>
                {{ Form::open(['id' => 'changeEmailSettingFrom']) }}
                <div class="row">
                    <div class="form-group col-12">
                        {{ Form::label('email_setting',__('messages.email_setting.email_setting') .':') }}<span
                                class="text-danger">*</span>
                        <span data-toggle="tooltip" data-html="true" title="" data-original-title="{{__('messages.email_setting.get_ticket_updates_through_email')}}"><i class="fas fa-question-circle"></i></span><br>
                        <label class="custom-switch pl-0 mt-2">
                            <input type="checkbox" name="email_setting" class="custom-switch-input">
                            <span class="custom-switch-indicator"></span>
                            <span class="custom-switch-description">{{__('messages.email_setting.send_me_ticket_updates_through_email')}}</span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        {{ Form::label('email_cases',__('messages.email_setting.email_cases') .':') }}
                        <ul>
                            @role('Admin')
                            <li>{{ __('messages.email_notification.making_ticket') }}</li>
                            @endrole
                            @role('Agent')
                            <li>{{ __('messages.email_notification.assigned_ticket') }}</li>
                            @endrole
                            @role('Customer')
                            <li>{{ __('messages.email_notification.create_ticket') }}</li>
                            @endrole
                            <li>{{ __('messages.email_notification.receive_reply_ticket') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'),['type' => 'submit','class' => 'btn btn-primary mr-2', 'id' => 'btnEmailSettingChange', 
'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
                    <button type="button" class="btn btn-light left-margin"
                            data-dismiss="modal">{{ __('messages.common.cancel') }} </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
