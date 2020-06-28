<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')

        <div class="input-group">

            @if ($prepend)
                <span class="input-group-addon">{!! $prepend !!}</span>
            @endif

            <input {!! $attributes !!} style="width: calc(100% - {!! $buttonWidth !!});"/>
            <a class="btn btn-info {{$buttonClass}}"
               {!! $buttonAttr !!} style="width:{!! $buttonWidth !!};{!! $buttonStyle !!}">{!! $buttonText !!}</a>
            @if ($append)
                <span class="input-group-addon clearfix">{!! $append !!}</span>
            @endif
        </div>

        @include('admin::form.help-block')

    </div>
</div>
