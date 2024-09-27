<div>
    <div class="row">
        <div class="col-md-12">
            <div wire:loading id="overlay-screen-lock">
                <div class="live-wire-infy-loader">
                    @include('loader')
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12">
            <div class="row mb-3 justify-content-end flex-wrap px-mobile-3">
                <div class="width-mobile-100">
                    <div class="selectgroup">
                        <input wire:model.debounce.100ms="searchByCategory" type="search"
                               id="searchByCategory"
                               autocomplete="off"
                               placeholder="{{ __('messages.category.search_category') }}"
                               class="form-control customer-dashboard-category-search show-overflow-ellipsis custom-input">
                    </div>
                </div>
            </div>
            <?php
            $style = 'style';
            $border = 'border-left: 4px solid ';
            $backColor = 'background-color';
            ?>
            @if(count($categories) > 0)
                <div class="content">
                    <div class="row position-relative">
                        @foreach($categories as $category)
                            <div class="col-12 col-sm-6 col-md-6 col-xl-4">
                                <div class="hover-effect-category position-relative mb-4 category-card-hover-border"
                                {{ $style }}="{{ $border }}{{ $category->color }}">
                                <div class="category-listing-details">
                                    <div class="d-flex category-listing-description">
                                        <div class="category-data">
                                            <h3 class="category-listing-title mb-1">
                                                <a href="{{ route('category.show',$category->id) }}"
                                                   class="users-listing-title text-decoration-none text-dark">{{ $category->name }}</a>
                                            </h3>
                                            <h3 class="category-listing-title d-flex align-items-center"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title="{{ __('messages.category.open_total_ticket') }}">
                                                <i class="fas fa-ticket-alt text-darkorange"></i>
                                                <span>
                                                    &nbsp;{{ $category->open_tickets_count. ' / ' . $category->ticket_count }} {{ __('messages.ticket.tickets') }}
                                                    </span>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="category-action-btn">
                                    <a title="<?php echo __('messages.common.edit')?>"
                                       class="action-btn edit-btn category-edit"
                                       data-id="{{ $category->id }}"
                                       href="#">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a ttitle="<?php echo __('messages.common.delete')?>"
                                       class="action-btn delete-btn category-delete"
                                       data-id="{{ $category->id }}"
                                       href="#">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                    </div>
                    @endforeach
                </div>
                    <div class="mt-0 mb-5 col-12">
                        <div class="row paginatorRow justify-content-between">
                            <div class="px-3">
                                <span class="d-inline-flex">
                                    {{ __('messages.common.showing') }}
                                    <span class="font-weight-bold ml-1 mr-1">{{ $categories->firstItem() }}</span> -
                                    <span class="font-weight-bold ml-1 mr-1">{{ $categories->lastItem() }}</span> {{ __('messages.common.of') }}
                                    <span class="font-weight-bold ml-1">{{ $categories->total() }}</span>
                                </span>
                            </div>
                            <div class="px-3 d-flex justify-content-end">
                                @if($categories->count() > 0)
                                    {{ $categories->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-lg-12 col-md-12 d-flex justify-content-center">
                    @if($searchByCategory == null || empty($searchByCategory))
                        <h1 class="font-size">{{ __('messages.category.categories_not_available') }}</h1>
                    @else
                        <h1 class="font-size">{{ __('messages.category.categories_no_found') }}</h1>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
