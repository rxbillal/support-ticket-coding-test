<div>
    <form class="mb-2 p-1">
        <input type="search" class="form-control search-input" id="searchBlockUsers"
               placeholder="{{ __('messages.search') }}..." wire:model="searchTerm">
    </form>
    <div id="divOfBlockedUsers">
        <ul class="list-group user-list-chat-select list-without-filter" id="blockedUsersList">
            @foreach($users as $key => $user)
                <li class="list-group-item blocked-user-list-chat-select__list-item blocked-user-{{ $user->id }} align-items-center d-flex justify-content-between">

                    <div class="d-flex">
                        <div class="new-conversation-img-status position-relative mr-2">
                            <div class="new-conversation-img-status__inner">
                                <img src="{{ $user->photo_url }}" alt="user-avatar-img"
                                     class="user-avatar-img add-user-img">
                            </div>
                        </div>
                        <div>
                            <span class="add-user-contact-name align-self-center">{{ $user->name }}</span>
                            <div class="align-self-center">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-success btn-unblock"
                                data-id="{{ $user->id }}">{{ __('messages.unblock') }}</button>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="text-center no-blocked-user new-conversation__no-user @if(count($users) > 0) d-none @endif">
            <div class="chat__not-selected">
                <div class="text-center"><i class="fa fa-2x fa-user text-primary" aria-hidden="true"></i>
                </div>
                <span id="noBlockedUsers">{{ __('messages.no_blocked_user_available') }}</span>
            </div>
        </div>
    </div>
</div>
