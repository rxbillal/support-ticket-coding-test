<div class="row">
    @foreach($selectedLangMessages as $key => $value)
        @if(!is_array($value))
            <div class="col-lg-3 col-md-3 mb-4">
                <label>{{ str_replace('_',' ',ucfirst($key)) }} :</label>
                <input type="text" class="form-control" name="{{$key}}" value="{{ $value }}"/>
            </div>
        @else
            @foreach($value as $nestedKey => $nestedValue)
                @if(!is_array($nestedValue))
                    <div class="col-lg-3 col-md-3 mb-4">
                        <label>{{ str_replace('_',' ',ucfirst($nestedKey)) }} :</label>
                        <input type="text" class="form-control" name="{{$key}}[{{$nestedKey}}]"
                               value="{{ $nestedValue }}"/>
                    </div>
                @endif
            @endforeach
        @endif
    @endforeach
</div>
