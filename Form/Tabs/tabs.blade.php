<div class="{{$viewClass['form-group']}} wen-tabs-box">
    @include('admin::form.error')
    <div class="wen-tabs-list">
        @foreach($tabs as $k => $tab)
            <div class="{{$tab['class']}}-tab-name wen-tabs-name"
                 data-class="{{$tab['class']}}" data-name="{{$tab['tabName']}}">
                <div class="tab-name-box @if($activeClass==$tab['class']) active-tab-font @endif">{{$tab['tabName']}}</div>
                <div class="active-tab-under-line @if($activeClass==$tab['class']) active-tab @endif"></div>
            </div>
        @endforeach
        @if($fieldName)
            <input type="text" name="{{$fieldName}}" value="{{$activeTab}}" hidden>
        @endif
    </div>
    <div class="wen-tabs-contents">
        @foreach($tabs as $k => $tab)
            <div class="{{$tab['class']}}-tab-content wen-tabs-content @if($activeClass==$tab['class']) wen-tabs-show @endif">
                {!! $tab['content'] !!}
            </div>
        @endforeach
    </div>

    @include('admin::form.help-block')
</div>
<script>
    $(".wen-tabs-name").click(function (e) {
        $(this).children('.active-tab-under-line').addClass('active-tab');
        $(this).children('.tab-name-box').addClass('active-tab-font');
        $(this).siblings().children('.active-tab-under-line').removeClass('active-tab');
        $(this).siblings().children('.tab-name-box').removeClass('active-tab-font');

        $("." + e.currentTarget.dataset.class + "-tab-content").addClass('wen-tabs-show');
        $("." + e.currentTarget.dataset.class + "-tab-content").siblings().removeClass('wen-tabs-show')

        $("input[name='{{$fieldName}}']").val(e.currentTarget.dataset.name);
                @if($eventFunc)
        var func = {!! $eventFunc !!};
        func(e, this);
        @endif
    });
</script>

<style>
    .wen-tabs-box {
        position: relative;
    }

    .wen-tabs-list {
        display: flex;
        border-bottom: 1px solid;
    }

    .wen-tabs-name {
        cursor: pointer;
        margin-left: 20px;
    }

    .tab-name-box {
        padding: 5px 15px;
    }

    .active-tab {
        border-bottom: 2px solid red;
    }

    .active-tab-font {
        font-weight: bold;
    }

    .wen-tabs-contents {
        margin-top: 20px;
        max-height: {{$maxHeight}};
        max-width: {{$maxWidth}};
        min-height: {{$minHeight}};
        min-width: {{$minWidth}};
        overflow: auto;
    }

    .wen-tabs-content {
        display: none;
    }

    .wen-tabs-show {
        display: block;
    }

    .active-tab-under-line {
        height: 0px;
        width: 50%;
        margin: auto;
    }
</style>