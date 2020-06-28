<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    @if($showLabel)
        <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    @endif
    <div class="{{$showLabel ? $viewClass['field']:''}}">
        @include('admin::form.error')
        <div class="checkbox-{{$unique}}" id="checkbox-{{$unique}}">
            {{--<ul class="tree-root-box-{{$unique}}">--}}
            {{--                {!! $optionsHtml !!}--}}
            {{--</ul>--}}
        </div>
        @include('admin::form.help-block')
    </div>
</div>

<link rel="stylesheet" href="/ynTree/yntree.min.css"/>
<script type="text/javascript" src="/ynTree/yntree.js"></script>
<script>
    //数据
    var datas = {!! $options !!};

    var treeData = {
// 复选框change事件
        onchange: function (input, yntree) {
            let {value, text, name, checked} = this;
                    @if($changeEvent)
            var func = {!! $changeEvent !!};
            func(value, name, text, checked);
            @endif
        },

        checkStrictly: true, //是否父子互相关联，默认true
        data: datas, //数据
    };

    /**下面是数据的初始化设置**/
    var yntree{{$unique}} = new YnTree(document.getElementById('checkbox-{{$unique}}'), treeData, {!! $configs !!});

            @if($javascriptFunc)
    var func = {!! $javascriptFunc !!};
    func(yntree{{$unique}}, treeData, 'checkbox-{{$unique}}');
    @endif
</script>

<style>
    .checkbox-{{$unique}} {
        overflow: auto;
        max-width: {{$maxWidth}};
        max-height: {{$maxHeight}};
        min-width: {{$minWidth}};
        min-height: {{$minHeight}};
    }
</style>