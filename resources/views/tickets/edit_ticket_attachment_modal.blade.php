<!-- Modal -->
<div class="modal fade" id="editAttachment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.upload_files') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('ticket.add-attachment', ['ticket' => $ticket->id]) }}"
                      class="dropzone ticket-attachment-dropzone"
                      id="editAttachmentDropzone">
                    {{ csrf_field() }}
                </form>
                <div class="d-flex mt-3 float-right">
                    <button id="save-file"
                            class="upload-file-btn btn btn-primary mr-2">{{ __('messages.upload_files') }}</button>
                    <button type="reset" id="cancel-upload-file"
                            class="upload-file-btn btn btn-light text-dark">{{ __('messages.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
