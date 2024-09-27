<div class="row justify-content-center no-gutters pt-5">
    <div class="col-md-8">
        <div wire:loading id="overlay-screen-lock">
            <div class="live-wire-infy-loader">
                @include('loader')
            </div>
        </div>
        <div class="card bg-white border-0 mb-0 faq-card">
            <div class="card-body px-lg-5 py-lg-5">
                <div class="text-center text-primary">
                    <h2 class="mb-3 text-18">{{ __('messages.faq.faqs') }}</h2>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row mb-3 justify-content-center flex-wrap px-front-mobile-3">
                            <div class="width-front-mobile-100 w-50">
                                <div class="selectgroup mr-3 w-100">
                                    <input wire:model.debounce.100ms="search" type="search" autocomplete="off"
                                           id="search" placeholder="{{ __('messages.search') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        @if(count($faqs) > 0)
                            <div id="accordion">
                                @foreach($faqs as $faq)
                                    <div class="accordion">
                                        <div class="accordion-header  collapsed p-3 shadow-sm" role="button"
                                             data-toggle="collapse" data-target="#panel-body-{{$loop->index}}"
                                             aria-expanded="false">
                                            <h4 class="mb-0 py-2 pl-3 text-primary">{{ $faq->title }}</h4>
                                        </div>

                                        <div class="accordion-body collapse text-justify p-3 faqs-description faqs-description-front"
                                             id="panel-body-{{$loop->index}}"
                                             data-parent="#accordion">
                                            <p class="mb-0 py-2 pl-3 text-justify">{!! $faq->description !!}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="float-right my-2">
                                @if($faqs->count() > 0)
                                    {{ $faqs->links() }}
                                @endif
                            </div>
                        @else
                            <div class="col-lg-12 col-md-12 d-flex justify-content-center">
                                <h1 class="font-size">{{ __('messages.faq.no_faqs_found') }}</h1>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
