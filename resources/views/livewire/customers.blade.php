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
    <div class="d-flex mb-3 justify-content-between flex-wrap custom-customer-input">
        <div class="mb-2 mr-3 d-flex flex-wrap">
            <div>
                <i class="fas fa-circle text-primary mx-2"></i><span>{{ __('messages.ticket.total_ticket') }}</span>
            </div>
            <div>
                <i class="fas fa-circle text-success mx-2"></i><span>{{ __('messages.admin_dashboard.open_tickets') }}</span>
            </div>
            <div>
                <i class="fas fa-circle text-warning mx-2"></i><span>{{ __('messages.admin_dashboard.in_progress_tickets') }}</span>
            </div>
            <div>
                <i class="fas fa-circle text-close-ticket mx-2"></i><span>{{ __('messages.admin_dashboard.closed_tickets') }}</span>
            </div>
        </div>
        <div class="ml-auto width-mobile-100">
            <div class="selectgroup">
                <input wire:model.debounce.100ms="searchByUser" type="search" id="searchByUser"
                       placeholder="{{ __('messages.customer.search_customer') }}" autocomplete="off"
                       class="form-control show-overflow-ellipsis custom-input">
            </div>
        </div>
    </div>
    <div class="users-card mt-5">
        @php
            /** @var \App\Models\User $user */
            $getLoggedInUserId = getLoggedInUserId();
        @endphp
        <div class="row">
            @forelse($users as $user)
                <div class="col-xl-4 col-md-6">
                    <div class="hover-effect-users position-relative mb-5 users-card-hover-border users-border">
                        <div class="users-listing-details">
                            <div class="d-flex users-listing-description align-items-center justify-content-center flex-row">
                                <div class="pl-0 mb-2 mr-sm-2 mr-0">
                                    <img src="{{ $user->photo_url }}" alt="user-avatar-img"
                                         class="img-responsive users-avatar-img users-img">
                                </div>
                                <div class="w-100 users-data mb-0">
                                    <div class="d-flex justify-content-sm-start justify-content-center align-items-center w-100">
                                        <div>
                                            <a href="{{ route('customer.show',$user->id) }}"
                                               class="users-listing-title text-decoration-none">{{ $user->name }}</a>
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex justify-content-sm-start justify-content-center">
                                        <a href="mailto:{{ $user->email }}"
                                           class="customer-email text-decoration-none">{{ $user->email }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex assigned-user justify-content-between pt-0">
                            <div class="d-flex">
                                <div class="counter-badge">
                                    <div class="text-center badge badge-primary font-weight-bold" data-toggle="tooltip"
                                         data-placement="top"
                                         title="{{ __('messages.ticket.total_ticket') }}">{{ $user->tickets_count }}</div>
                                </div>
                                <div class="counter-badge">
                                    <div class="text-center badge badge-success font-weight-bold" data-toggle="tooltip"
                                         data-placement="top"
                                         title="{{ __('messages.admin_dashboard.open_tickets') }}">{{ $user->active_tickets_count }}</div>
                                </div>
                                <div class="counter-badge">
                                    <div class="text-center badge badge-warning font-weight-bold" data-toggle="tooltip"
                                         data-placement="top"
                                         title="{{ __('messages.admin_dashboard.in_progress_tickets') }}">{{ $user->in_progress_tickets_count }}</div>
                                </div>
                                <div class="counter-badge">
                                    <div class="text-center badge close-ticket-badge font-weight-bold text-white"
                                         data-toggle="tooltip"
                                         data-placement="top"
                                         title="{{ __('messages.admin_dashboard.closed_tickets') }}">{{ $user->close_tickets_count }}</div>
                                </div>
                            </div>
                            <label class="custom-switch">
                                <input type="checkbox" name="email_verified"
                                       class="custom-switch-input email-verification-toggle"
                                       {{ is_null($user->email_verified_at) ? '' : 'checked' }} {{ is_null($user->email_verified_at) ? '' : 'disabled' }} data-id="{{ $user->id }}">
                                <span class="custom-switch-indicator"
                                      title="<?php echo __('messages.placeholder.verify_user_email')?>"></span>
                            </label>
                        </div>
                        <div class="users-action-btn">
                            <a title="<?php echo __('messages.common.edit')?>"
                               class="action-btn edit-btn users-edit"
                               href="{{ route('customer.edit',$user->id) }}">
                                <i class="fa fa-edit"></i>
                            </a>
                            @if($getLoggedInUserId != $user->id)
                                <a ttitle="<?php echo __('messages.common.delete')?>"
                                   class="action-btn customer-delete-btn users-delete"
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
                        <h1 class="font-size">{{ __('messages.customer.no_customers_available') }}</h1>
                    @else
                        <h1 class="font-size">{{ __('messages.customer.customer_not_found') }}</h1>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
    <div class="mt-0 mb-5 col-12">
        <div class="row paginatorRow">
            <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
            <span class="d-inline-flex">
                @if($users->total())
                    {{ __('messages.common.showing') }}
                    <span class="font-weight-bold ml-1 mr-1">{{ $users->firstItem() }}</span> -
                    <span
                            class="font-weight-bold ml-1 mr-1">{{ $users->lastItem() }}</span> {{ __('messages.common.of') }}
                    <span class="font-weight-bold ml-1">{{ $users->total() }}</span>
                @endif
            </span>
            </div>
            <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                @if($users->count() > 0)
                    {{ $users->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
