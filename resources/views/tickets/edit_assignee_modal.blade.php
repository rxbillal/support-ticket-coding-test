<div id="EditAssigneeModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.common.edit').' '. __('messages.agent.agents') }}</h5>
                <button type="button" aria-label="Close" class="close outline-none" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="editValidationErrorsBox"></div>
                <div class="row">
                    <input type="hidden" id="hiddenTicketId">
                    <div class="form-group col-sm-12">
                        {{ Form::label('assign_to', __('messages.agent.agents').':') }}
                        {{ Form::select('assignees[]',$users, $assignees, ['class' => 'form-control','id'=>'txtEditAssignee', 'multiple' => true]) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type' => 'button', 'class' => 'btn btn-primary ml-1', 'id' => 'btnSaveAssignees', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
                    <button type="button" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
