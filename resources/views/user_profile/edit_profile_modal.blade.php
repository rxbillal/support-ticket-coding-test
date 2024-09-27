<div id="editProfileModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content left-margin">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.user.edit_profile') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            {{ Form::open(['id'=>'editProfileForm','files'=>true, 'autocomplete' => 'off']) }}
            <div class="modal-body">
                {{ Form::hidden('user_id',null,['id'=>'editUserId']) }}
                {{csrf_field()}}
                <div class="row">
                    <div class="form-group col-sm-6">
                        {{ Form::label('name', __('messages.common.name').':') }}<span
                                class="text-danger">*</span>
                        {{ Form::text('name', null, ['id'=>'firstName','class' => 'form-control','required']) }}
                    </div>
                    <div class="form-group col-sm-6">
                        {{ Form::label('email',__('messages.common.email').':') }}<span class="text-danger">*</span>
                        {{ Form::email('email', null, ['id'=>'userEmail','class' => 'form-control','required']) }}
                    </div>

                    <div class="form-group col-sm-6">
                        <span id="profilePictureValidationErrorsBox" class="text-danger d-none"></span>
                        <div class="row">
                            <div class="col-4 col-sm-4 col-xl-3">
                                {{ Form::label('profile_picture', __('messages.user.profile').':') }}
                                <label class="image__file-upload text-white"> {{ __('messages.common.choose') }}
                                    {{ Form::file('image',['id'=>'profilePicture','class' => 'd-none']) }}
                                </label>
                            </div>
                            <div class="col-3 pl-0 mt-1 float-right">
                                <img id='profilePicturePreview' class="thumbnail-preview w-75 user-edit-profile-img"
                                     src="{{ asset('assets/img/infyom-logo.png') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-sm-6">
                        {{ Form::label('phone', __('messages.common.phone').':') }}<span
                                class="text-danger">*</span><br>
                        <div class="d-flex">
                            <div class="region-code">
                                <button type="button"
                                        class="btn btn-default mr-0 f16 dropdown-toggle region-code-button"
                                        id="dropdownMenuButton" data-toggle="dropdown">
                                    <span class="flag edit_profile_flag" id="btnFlag"></span>
                                    <span class="btn-cc editProfileBtnCc">&nbsp;&nbsp;+91&nbsp;&nbsp;</span>
                                    <span class="caretButton"></span>
                                </button>
                                <div class="region-code-div" aria-labelledby="dropdownMenuButton">
                                    <ul class="f16 dropdown-menu region-code-ul">
                                        <div class="region-code-ul-input-div"><input type="text"
                                                                                     class="form-control search-country"/>
                                        </div>
                                        <div class="region-code-ul-div"></div>
                                    </ul>
                                </div>
                            </div>
                            <input type="tel" class="form-control" name="phone" id="userPhone"
                                   onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                   required/>
                            <input type="hidden" name="region_code" class="edit_profile_region_code" id="regionCode"
                                   value="91"/>
                            <input type="hidden" name="region_code_flag" class="edit_profile_region_code_flag"
                                   id="regionCodeFlag" value="in"/>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnPrEditSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
                    <button type="button" class="btn btn-light left-margin"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
