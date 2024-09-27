<div class="tickets">

    <div class="ticket-content w-100">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="ticket-header">
                    <div class="ticket-sender-picture img-shadow">
                        <img src="{{ $ticket->user->photo_url }}" class="object-fit-cover" alt="image">
                    </div>
                    <div class="ticket-detail">
                        <div class="ticket-title">
                            <h4>{{$ticket->title}}</h4>
                        </div>
                        <div class="ticket-info">
                            <div class="bullet"></div>
                            <div class="font-weight-600">{{$ticket->ticket_id}}</div>
                            <div class="bullet"></div>
                            <div class="text-primary font-weight-600">{{ $ticket->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
                <div class="ticket-description">
                    <p>
                        {!! $ticket->description !!}
                    </p>
                    <div class="gallery">
                        @foreach(range(1,5) as $index)
                            <a class="gallery-item" data-title="Image {{$index}}"
                               data-image="{{ asset('theme-assets/img/img01.jpg') }}">
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="ticket-divider"></div>
                <div class="ticket-form">
                    @include('flash::message')
                    <div class="form-group">
                        <form>
                            <textarea wire:model="description" id="description" class="form-control"
                                      placeholder="{{__('messages.ticket.type_replay')}}"></textarea>
                            @error('description')
                            <span class="text-danger pt-1 pl-2">{{ $message }}</span>
                            @enderror

                            <div class="form-group text-right">
                                <button wire:click.prevent="store()" class="btn btn-primary btn-lg mt-5">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-12">
                <div class="activities fixed-height-activities">
                    @forelse($replays->reverse() as $replay)
                        <div class="activity" data-id="{{ $replay->id }}">
                            <div class="activity-icon bg-primary text-white shadow-primary">
                                <i class="fas fa-comment-alt"></i>
                            </div>
                            <div class="activity-detail w-100">
                                <div class="mb-2">
                                    <span class="text-job text-primary">{{ $replay->created_at->diffForHumans() }}</span>
                                    <span class="bullet"></span>
                                    <span class="text-job">By {{ $replay->user->name }}</span>
                                    <div class="float-right dropleft  dropdown ml-4">
                                        <a href="#" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-h "></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-title">{{__('messages.placeholder.options')}}</div>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" wire:click="destory({{ $replay->id }})"
                                               class="dropdown-item has-icon text-danger"
                                               data-id="{{ $replay->id }}"> <i
                                                        class="fas fa-trash-alt"></i>
                                                {{ __('messages.common.delete') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                {!! $replay->description !!}
                            </div>
                        </div>
                    @empty
                        <h6 class="ml-5" id="notfoundReplay">No Replay Avalibal.</h6>
                    @endforelse
                </div> {{--  End actitvites  --}}
            </div>
        </div>

    </div> {{--  End ticket-content  --}}
</div> 
