<div>
    <form class="mb-2 p-1">
        <input type="search" class="form-control search-input" autocomplete="off" id="searchContactForChat"
               placeholder="{{ __('messages.search') }}..." wire:model="searchTerm">
    </form>
    <div class="form-group">
        <div class="col-sm-12 d-flex justify-content-around">
            <div class="custom-control custom-checkbox">
                <input name="new_contact_gender" value="1" type="checkbox" class="custom-control-input group-type"
                       id="newContactMale" wire:model="male">
                <label class="custom-control-label" for="newContactMale">{{ __('messages.common.male') }}</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input name="new_contact_gender" value="2" type="checkbox" class="custom-control-input group-type"
                       id="newContactFemale" wire:model="female">
                <label class="custom-control-label" for="newContactFemale">{{ __('messages.common.female') }}</label>
            </div>
        </div>
    </div>
    <div id="userListForAddPeople">
        <ul class="list-group user-list-chat-select list-with-filter " id="{{ ($isAssignToAgent) ? 'userListForAssignChat' : 'userListForChat' }}">
            @foreach($users as $key => $value)
                <li class="list-group-item user-list-chat-select__list-item align-items-center chat-user-{{ $value->id }} {{ getGender($value->gender) }}"
                    data-status="{{ $value->is_online }}" data-gender="{{$value->gender}}">
                    <input type="hidden" class="add-chat-user-id" value="{{ $value->id }}">
                    <input type="hidden" class="add-chat-is-customer-chat" value="{{ $value->is_system }}">
                    <input type="hidden" class="add-chat-user-role" value="{{ $value->role_name }}">
                    <div class="new-conversation-img-status position-relative mr-2 user-{{ $value->id }}"
                         data-status="{{ $value->is_online }}">
                        <div class="chat__person-box-status @if($value->is_online) chat__person-box-status--online @else chat__person-box-status--offline @endif"></div>
                        <div class="new-conversation-img-status__inner">
                            <img src="{{ $value->photo_url }}" alt="user-avatar-img"
                                 class="user-avatar-img add-user-img">
                        </div>
                    </div>
                    <div>
                        <span class="add-user-contact-name align-self-center">{{ $value->name }}</span>
                        <div class="align-self-center">{{ $value->email }}</div>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="text-center no-user new-conversation__no-user @if(count($users) > 0) d-none @endif">
            <div class="chat__not-selected">
                <div class="text-center"><i class="fa fa-2x fa-user text-primary" aria-hidden="true"></i>
                </div>
                {{ $isAssignToAgent ? __('messages.no_agent_found') : __('messages.no_user_found') }}
            </div>
        </div>
    </div>
</div>
