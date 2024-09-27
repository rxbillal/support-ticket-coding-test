<div>
    <div class="container wow fadeInUp">
        <h2 class="text-center text-primary mt-5 mb-5">{{ __('messages.category.categories') }}</h2>
        <div class="row justify-content-center">
            @php
                $inStyle = 'style';
                $style = 'border-top: 2px solid';
                $styleBackground = 'background: ';
            @endphp
            @foreach($categories as $key => $category)
                <div class="col-12 col-md-6 col-lg-4 wow fadeInUp" data-wow-duration="1s">
                    <div class="card card-primary" {{$inStyle}}="{{$style}} {{ getNewColor($key) }}">
                    <a href="{{ route('public.tickets',['category' => $category->name]) }}"
                       class="text-decoration-none">
                        <div class="card-header">
                            <h4>{{ $category->name }}</h4>
                        </div>
                    </a>
                    <div class="card-body categories-count" {{$inStyle}}="{{ $styleBackground }}{{ getNewColor($key) }}"
                    >
                    <h6 class="m-0">{{ $category->ticket_count }}</h6>
                </div>
        </div>
    </div>
    @endforeach
</div>
</div>
<div class="mt-0 mb-5 col-12">
    <div class="row paginatorRow">
        <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                <span class="d-inline-flex">
                    {{ __('messages.common.showing') }} 
                    <span class="font-weight-bold ml-1 mr-1">{{ $categories->firstItem() }}</span> - 
                    <span class="font-weight-bold ml-1 mr-1">{{ $categories->lastItem() }}</span> {{ __('messages.common.of') }} <span
                            class="font-weight-bold ml-1">{{ $categories->total() }}</span>
                </span>
        </div>
        <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
            {{ $categories->links() }}
        </div>
    </div>
</div>
</div>
