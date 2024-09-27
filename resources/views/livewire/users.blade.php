<div>
    <div class="row">
        <div class="col-md-12">
            <div wire:loading id="overlay-screen-lock">
                <div class="live-wire-infy-loader">
                    @include('loader')
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex mb-3 justify-content-end flex-wrap justify-content-mobile-center">
        <div class="width-mobile-100">
            <div class="selectgroup">
                <input wire:model.debounce.100ms="searchByUser" type="search" id="searchByUser"
                       placeholder="{{ __('messages.agent.search_agent') }}" autocomplete="off"
                       class="form-control show-overflow-ellipsis custom-input">
            </div>
        </div>
    </div>
    <div class="users-card">
        <div class="row">
            @php
                /** @var \App\Models\User $user */
                $adminRoleID = getAdminRoleId();
                $agentRoleID = getAgentRoleId()
            @endphp
            @forelse($users as $user)
                <div class="col-xl-4 col-md-6">
                    <div class="hover-effect-users position-relative mb-5 pb-md-0 pb-4 border-hover-primary users-border">
                        <div class="users-listing-details">
                            <div class="d-flex users-listing-description align-items-center justify-content-center flex-row">
                                <div class="pl-0 mb-2 mr-sm-2 mr-0">
                                    <img src="{{ $user->photo_url }}" alt="user-avatar-img"
                                         class="img-responsive users-avatar-img users-img">
                                </div>
                                <div class="mb-0 w-100 users-data">
                                    <div class="d-flex justify-content-sm-start justify-content-center align-items-center w-100">
                                        <div>
                                            <a href="{{ route('user.show',$user->id) }}"
                                               class="users-listing-title text-decoration-none">{{ $user->name }}</a>
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex justify-content-sm-start justify-content-center">
                                        <a href="mailto:{{ $user->email }}"
                                           class="text-decoration-none text-color-gray">{{ $user->email }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-sm-start justify-content-center mb-md-0 mb-2 align-items-center assigned-user pt-0 mx-3">
                            <div class="d-flex justify-content-between w-100">
                                <div>
                                    <span class="badge badge-primary text-uppercase rounded-circle assigned-user-badge mr-1">{{ $user->ticket_count }}</span>
                                    {{ __('messages.user.assigned_tickets') }}
                                </div>
                                <label class="custom-switch">
                                    <input type="checkbox" name="email_verified"
                                           class="custom-switch-input email-verification-toggle"
                                           {{ is_null($user->email_verified_at) ? '' : 'checked' }} {{ is_null($user->email_verified_at) ? '' : 'disabled' }} data-id="{{ $user->id }}">
                                    <span class="custom-switch-indicator"
                                          title="<?php echo __('messages.placeholder.verify_user_email') ?>"></span>
                                </label>
                            </div>
                        </div>
                        <div class="users-action-btn">
                            <a title="<?php echo __('messages.common.edit') ?>"
                               class="action-btn edit-btn users-edit"
                               href="{{ route('agent.edit',$user->id) }}">
                                <i class="fa fa-edit"></i>
                            </a>
                            @if(getLoggedInUserId() != $user->id)
                                <a title="<?php echo __('messages.common.edit') ?>"
                                   class="action-btn delete-btn users-delete"
                                   data-id="{{ $user->id }}"
                                   href="#">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12 d-flex justify-content-center mt-3">
                    @if($searchByUser == null || empty($searchByUser))
                        <h1 class="font-size">{{ __('messages.agent.no_agents_available') }}</h1>
                    @else
                        <h1 class="font-size">{{ __('messages.agent.agent_not_found') }}</h1>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
    <div class="mt-0 mb-5 col-12">
        <div class="row paginatorRow">
            <div class="col-lg-2 col-md-6 col-sm-12 pt-2 mb-3">
            <span class="d-inline-flex">
                {{ __('messages.common.showing') }}
                <span class="font-weight-bold ml-1 mr-1">{{ $users->firstItem() }}</span> -
                <span class="font-weight-bold ml-1 mr-1">{{ $users->lastItem() }}</span> {{ __('messages.common.of') }}
                <span class="font-weight-bold ml-1">{{ $users->total() }}</span>
            </span>
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end justify-content-center-xs">
                @if($users->count() > 0)
                    {{ $users->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
