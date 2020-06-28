<style>
    .sub-options-box li {
        list-style: none;
        margin: .5em 0px;
        padding: 0px;
    }

    .sub-options-box {
        padding: 0px;
        margin: 0px 0px 0px 2em;
    }

    .one-option-box {
        display: flex;
    }

    .select2-box-wen {
        position: relative;
    }

    .sub-box-wen {
        width: 100%;
        position: absolute;
        /*top: calc(100% + 0.1em);*/
        left: 0px;
        z-index: 100;
        overflow: hidden;
    }

    .sub-box-wen-top {
        bottom: calc(100% + 0.1em);
    }

    .sub-box-wen-bottom {
        top: calc(100% + 0.1em);
    }

    .show-checked-box-wen {
        min-height: 50px;
        width: 100%;
    }

    .checked-content-box-wen {
        width: 100%;
        height: 200px;
    }


    .sub-box-checkbox-wen {
        border: 1px solid lightskyblue;
        /*box-shadow: 0px 0px 2px deepskyblue;*/
        background: whitesmoke;
        padding: 1em;
    }

    .sub-box-checkbox-wen > ul {
        margin: 0px;
    }


    .checkbox-box-wen {
        position: relative;
        width: 2em;
    }

    .checkbox-input-wen,
    .checkbox-fake-box {
        display: block;
        width: 1.5em;
        height: 1.5em;
        cursor: pointer;
        position: absolute;
        top: 0px;
        left: 0px;
        background: deepskyblue;
        z-index: 99;
    }

    .checkbox-input-wen:checked + .checkbox-fake-box::before {
        display: block;
        content: "\2713";
        text-align: center;
        font-size: 16px;
        color: white;
    }

    .checkbox-input-wen {
        visibility: hidden;
    }

    .last-level-box {
        display: flex;
        justify-content: left;
        align-items: center;
        flex-wrap: wrap;
    }

    .last-level-box > li {
        margin-left: 1em;
    }

    .last-level-box > li:nth-child(1) {
        margin-left: 0px;
    }

    .show-checked-box-wen .sub-box-checkbox-wen {
        /*box-shadow: 0px 0px 2px gray;*/
    }

    .sub-box-scroll {
        box-shadow: 0px 0px 2px gray;
    }

    .show-checked-box-wen .checkbox-fake-box {
        background: red;
    }

    .hide {
        visibility: hidden;
    }

    .title-box-wen {
        display: block;
        width: 100%;
        text-align: center;
        color: red;
        background: lightblue;
        box-shadow: 0px 0px 2px lightskyblue;
        position: absolute;
        top: 0px;
        left: 0px;
        line-height: 35px;
        font-weight: bold;
    }

    .sub-options-box-tttt {
        padding: 25px 0px;
        box-sizing: border-box;
    }

    .sub-options-box-tttt > .sub-options-box-bbbb {
        overflow: auto;
    }

    .sub-box-wen-tips-box {
        padding: 25px 0px;
        box-sizing: border-box;
        background: whitesmoke;
    }

    .sub-box-wen-tips-box .tips {
        text-align: center;
        color: gray;
        font-size: 2em;
    }


</style>
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!} select2-box-wen">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}} wen-selected-box">

        @include('admin::form.error')

        <select class="form-control {{$class}} wen-selected-box-{{$unique_key}}" data-alias="{{$select_alias}}"
                style="width: 100%;"
                name="{{$name}}[]" multiple
                data-placeholder="{{ $placeholder }}" {!! $attributes !!} >
            @foreach($options as $select => $option)
                <option value="{{$select}}" {{  in_array($select, (array)old($column, $value)) ?'selected':'' }}>{{$option}}</option>
            @endforeach
        </select>
        <input type="hidden" name="{{$name}}[]"/>

        @include('admin::form.help-block')
        <div class="sub-box-scroll sub-box-wen hide">
            <div class=" sub-box-checkbox-wen "></div>
            <div class="sub-box-wen-tips-box hide">
                <p class="title-box-wen "></p>
                <div class="tips">
                    <i class="fa fa-frown-o"></i>
                    <div class="content-wen">空空如也</div>
                </div>
            </div>
        </div>
    </div>


</div>
<div class="show-checked-box-wen hide">
    <label class="{{$viewClass['label']}} control-label"></label>
    <div class="{{$viewClass['field']}} sub-box-checkbox-wen sub-box-checked-{{$unique_key}}"></div>
</div>
<script>
    let options_checked_{{$unique_key}} = {!! $default_checked_value !!}, // 勾选的值默认值

        options_cache_{{$unique_key}} = {},// 子选项缓存
        stop_to_options_{{$unique_key}} = false, // 鼠标是否停留在某个选项中
        checked_values_{{$unique_key}} = {!! $checked_values !!}; // 选中的选项
    {{--let sub_columns_{{$unique_key}} = {!! $sub_columns !!}, // 子字段级别顺序--}}
    {{--alias_map_{{$unique_key}} = {!! $alias_map !!}, // 别名映射--}}
    {{--sub_options_{{$unique_key}} = {!! $sub_options !!}; // 子选项请求路由--}}


    $(function () {
        loadSubOptions();
        updateChecked();

        // 第一级鼠标经过事件
        $(document).on('mouseover', '.select2-selection__choice', function (e) {
            let classList = $(this).parent().parent().parent().parent().siblings('select')[0].classList.value;
            if (classList.indexOf('wen-selected-box-{{$unique_key}}') >= 0) {
                stop_to_options_{{$unique_key}} = true;
                let subWenObj = document.querySelector('.sub-box-wen');
                let obj = document.querySelector('.wen-selected-box-{{$unique_key}}');
                let h = setTimeout(function () {
                    if (stop_to_options_{{$unique_key}}) {
                        for (var dex in obj.options) {
                            if (obj.options[dex].innerText == e.currentTarget.title) {
                                let subObj = document.querySelector('.sub-box-checkbox-wen .' + obj.options[dex].value);
                                subWenObj.classList.remove('hide');
                                if (subObj) {
                                    subObj.classList.remove('hide');
                                    $(subObj).siblings().addClass('hide');
                                    $('.sub-box-wen-tips-box').addClass('hide');
                                } else {
                                    $('.sub-box-scroll ul').addClass('hide');
                                    $('.sub-box-wen-tips-box>.title-box-wen').html(e.currentTarget.title);
                                    $('.sub-box-wen-tips-box').removeClass('hide');
                                }
                            }
                        }
                    }
                    clearTimeout(h);
                }, 400)
                let winHeight = document.body.clientHeight - $(document).scrollTop();
                let bottom = (document.body.clientHeight - $(obj).offset().top - ($(obj).siblings('span').height())) * 0.8;
                let top = (winHeight - bottom - ($(obj).siblings('span').height())) * 0.80;
                if (top > bottom) {
                    subWenObj.classList.add('sub-box-wen-top');
                    subWenObj.style['max-height'] = top + 'px';
                    $('.sub-options-box-tttt >.sub-options-box-bbbb').css('max-height', (top - 35) + 'px')
                } else {
                    subWenObj.classList.add('sub-box-wen-bottom')
                    subWenObj.style['max-height'] = bottom + 'px';
                    $('.sub-options-box-tttt>.sub-options-box-bbbb').css('max-height', (bottom - 35) + 'px')
                }
            }
        });

        // 离开事件以及标签
        $(document).on('mouseleave', '.select2-selection__choice', function (e) {
            let classList = $(this).parent().parent().parent().parent().siblings('select')[0].classList.value;
            // 判断是否为当前页面的标签触发事件
            if (classList.indexOf('wen-selected-box-{{$unique_key}}') >= 0) {
                stop_to_options_{{$unique_key}} = false;
            }
        });

        // 隐藏选项框
        $(document).on('mouseleave', '.wen-selected-box', function () {
            document.querySelector('.sub-box-wen').classList.add('hide');
            document.querySelector('.sub-box-wen ul') ? document.querySelector('.sub-box-wen ul').classList.add('hide') : '';
            $('.sub-box-wen-tips-box').addClass('hide');
        })

        /**
         * 选中复选框值改变事件
         */
        $(document).on('change', '.show-checked-box-wen input[type=checkbox]', function (e) {
            let level = e.currentTarget.dataset.level.split('-');
            let dataset = e.currentTarget.dataset;
            deleteSelectedValues(level);
            options_checked_{{$unique_key}} = deleteObj(options_checked_{{$unique_key}}, level);
            updateChecked();
            $(".wen-selected-box-{{$unique_key}}").siblings('span').find('li[title="' + dataset.label + '"]').children('span').click();
            if (Object.keys(options_checked_{{$unique_key}}).length) {
                $('body>.select2-container--open ul').html('');
            }
        });

        /**
         * 选项复选框值改变事件
         */
        $(document).on('change', '.sub-box-wen input[type=checkbox]', function (e) {
            let level = e.currentTarget.dataset.level.split('-');
            if (e.currentTarget.checked) {
                options_checked_{{$unique_key}}  = addObj(options_cache_{{$unique_key}}, level, options_checked_{{$unique_key}});
            } else {
                deleteObj(options_checked_{{$unique_key}}, level);
                $(this).parent().parent().siblings('ul').find('input[type="checkbox"]').removeAttr('checked')
            }
            updateChecked();
        });


        /**
         * select复选下拉框值改变事件
         */
        $(".wen-selected-box-{{$unique_key}}").on('change', function (e) {
            loadSubOptions(true);
        });
    })


    // 清空某个以及选项下的所有子选项
    function bindCheckedToSubOptions(data, index) {
        if (data && data.column) {
            let obj = document.querySelector('#' + data.column + '-' + index + '-options');
            obj ? ($(obj).prop('checked', false) && $(obj).removeAttr('checked')) : '';
            obj ? ($(obj).parent().parent().siblings('ul').find('input[type="checkbox"]').prop('checked', false) && $(obj).parent().parent().siblings('ul').find('input[type="checkbox"]').removeAttr('checked')) : '';
        }

    }

    /**
     * input[type="checkbox"]:checked 与 select[multiple]同步绑定
     * @param values
     */
    function deleteSelectedValues(values) {
        let hadChecked = checked_values_{{$unique_key}};
        let data = options_checked_{{$unique_key}};
        if (typeof values != 'object') {
            values = values.split('-');
        }
        values.forEach(function (item, index) {
            data = data[item] ? data[item] : data.sub[item];
        });
        let level = values.join('-');
        let index = hadChecked.indexOf(level);
        if (index >= 0) {
            checked_values_{{$unique_key}}.splice(index, 1);
        }
        let dd = document.querySelector('#' + data.column + '-' + level + '-options');
        dd ? ($(dd).prop('checked', false) && $(dd).removeAttr('checked')) : '';
        clearSubOptionsChecked(data.sub, level)
    }

    // 清空子选项中打钩的复选框
    function clearSubOptionsChecked(data, level) {
        if (Object.keys(data).length) {
            let hadChecked = checked_values_{{$unique_key}};
            for (var dex in data) {
                let le = level + '-' + dex;
                let index = hadChecked.indexOf(le);
                if (index >= 0) {
                    checked_values_{{$unique_key}}.splice(index, 1);
                }

                let dd = document.querySelector('#' + data[dex].column + '-' + le + '-options');
                dd ? ($(dd).prop('checked', false) && $(dd).removeAttr('checked')) : '';
                if (Object.keys(data[dex].sub).length) {
                    clearSubOptionsChecked(data[dex].sub, le);
                }
            }
        }
    }

    // 新增对象属性
    function addObj(obj, name, origin_obj, dex = '') {
        if (!(name instanceof Array)) {
            throw new Error('参数name不正确,请输入一个一维数组');
        }
        let index = name.shift();
        dex += (dex ? '-' + index : index);
        if (obj[index] && obj[index].column) {
            let dd = document.querySelector('#' + obj[index].column + '-' + dex + '-options');
            dd ? dd.checked = true : '';

            if (!origin_obj[index]) {
                origin_obj[index] = {
                    column: obj[index].column,
                    label: obj[index].label,
                    sub: {},
                };
            }
            if (name.length) {
                origin_obj[index].sub = addObj(obj[index].sub, name, origin_obj[index].sub, dex)
            } else {
                origin_obj[index].sub = {};
            }
        }
        return origin_obj;
    }


    /**
     * 删除（多级）对象中某个指定属性
     * @param obj
     * @param name
     * @returns {*}
     */
    function deleteObj(obj, name) {
        if (!(name instanceof Array)) {
            throw new Error('参数name不正确,请输入一个一维数组');
        }
        let index = name.shift();
        if (obj[index] || (obj.sub && obj.sub[index])) {
            if (name.length) {
                if (obj.sub) {
                    obj.sub[index] = deleteObj(obj.sub[index], name);
                } else {
                    obj[index] = deleteObj(obj[index], name);
                }
            } else {
                bindCheckedToSubOptions(obj[index] ? obj[index] : obj.sub[index], index)
                obj[index] ? delete obj[index] : delete obj.sub[index];
            }
        }
        return obj;
    }


    /**
     * 更新选中的列表
     */
    function updateChecked() {
        if (Object.keys(options_checked_{{$unique_key}}).length) {
            document.querySelector('.show-checked-box-wen').classList.remove('hide');
        } else {
            document.querySelector('.show-checked-box-wen').classList.add('hide');
        }
        document.querySelector('.sub-box-checked-{{$unique_key}}').innerHTML = appendUl(options_checked_{{$unique_key}}, '', 'checked')
    }


    /**
     * 更新子选择框的列表
     */
    function updateSubOptions() {
        let data = options_cache_{{$unique_key}};
        let parentObj = document.querySelector('.sub-box-checkbox-wen');
        for (var dex in data) {
            if (!document.querySelector('.' + dex)) {
                parentObj.innerHTML += appendUl(data[dex].sub, dex, '', dex, data[dex].label)
            }
        }
    }


    /**
     * 创建子复选框列表
     * @param data
     * @param level
     * @param checked
     * @param custom_class
     * @returns {string}
     */
    function appendUl(data, level = '', checked = '', custom_class = '', title = '') {
        if (title) {
            title = '<p class="title-box-wen">' + title + '<\/p>'
        }
        let liAppendRes = appendLi(data, level, checked);
        if (!liAppendRes.li_html) {
            return '';
        }
        let html = liAppendRes.li_html + '<\/div><\/ul>';
        if (liAppendRes.last_level === true) {
            html = '<ul class="sub-options-box last-level-box ' + custom_class + (checked ? '' : custom_class ? ' hide sub-options-box-tttt ' : '') + ' ">' + title + '<div class="sub-options-box-bbbb">' + html;
        } else {
            html = '<ul class="sub-options-box ' + custom_class + (checked ? '' : custom_class ? ' hide sub-options-box-tttt ' : '') + '">' + title + '<div class="sub-options-box-bbbb">' + html;
        }
        return html;
    }

    /**
     * 创建子复选框选项
     * @param data
     * @param level
     * @param checked
     * @returns li_html: string, last_level: boolean
     */
    function appendLi(data, level = '', checked = '') {
        let liHtml = '', last_level = true;

        for (var index in data) {
            let le = (level ? level + '-' : '') + index;
            liHtml += '<li >' +
                '          <div class="one-option-box">' +
                '              <div class="checkbox-box-wen">' +
                '                  <input id="' + data[index].column + '-' + le + (checked ? '' : '-options') + '" type="checkbox" ' + (checked ? 'name="' + data[index].column + '[]"' : '') + ' class="checkbox-input-wen" ' + (checked || checkSubOptions(le) ? ' checked ' : ' ') + ' value="' + index + '"  data-level="' + le + '" data-column="' + data[index].column + '" data-label="' + data[index].label + '" >' +
                '                  <label class="checkbox-fake-box" for="' + data[index].column + '-' + le + (checked ? '' : '-options') + '"><\/label>' +
                '              <\/div>' +
                '              <div>' + data[index].label + '<\/div>' +
                '          <\/div>';
            if (data[index].sub && Object.keys(data[index].sub).length) {
                liHtml += appendUl(data[index].sub, le, checked);
                last_level = false;
            }
            liHtml += '    <\/li>';
        }
        return {
            "li_html": liHtml,
            "last_level": last_level,
        };
    }


    function checkSubOptions(level) {
        if (checked_values_{{$unique_key}}.indexOf(level) >= 0) {
            return true;
        }
        return false;
    }

    /**
     * 发送ajax请求
     * @param data
     * @param url
     * @param callback
     */
    function ajaxRequest({
                             data,
                             url,
                             callback
                         }) {
        $.ajax({
            url: url,
            dataType: 'json',
            data: data,
            success: function (rst) {
                callback && callback(rst);
            },
            fail: function (err) {
                console.error(err);
            }
        })
    }

    /**
     * 获取select[multiple]选中的值
     * @param filter // 是否过滤已经缓存过的值
     * @returns {any[]}
     */
    function getSelectedValues(filter = false) {
        let options = document.querySelector('.wen-selected-box-{{$unique_key}}').options;
        let values = new Array();
        for (var index in options) {
            let item = options[index];
            if (item.selected) {
                if (filter) {
                    if (Object.keys(options_cache_{{$unique_key}}).indexOf(item.value) < 0) {
                        values.push(item.value);
                    }
                } else {
                    values.push(item.value);
                }
            }
        }
        return values;
    }

    /**
     * 加载子复选框
     */
    function loadSubOptions(isChange = false) {
        let values = getSelectedValues(true);
        console.log('values', values);
        if (values.length) {
            ajaxRequest({
                data: {
                    '{{$name}}': values,
                },
                url: '{{$request_url}}',
                callback: function (rst) {
                    console.log('request', rst);
                    options_cache_{{$unique_key}} = Object.assign(options_cache_{{$unique_key}}, rst);
                    updateSubOptions();
                    if (isChange) {
                        updateCheckedBox();
                    }
                }
            });
        } else if (isChange) {
            updateCheckedBox();
        }
    }

    /**
     * 更新更新已选列表
     */
    function updateCheckedBox() {
        // 已选的值
        let data = options_checked_{{$unique_key}};
        let keys = Object.keys(data);
        // 获取select[multiple]的值
        let values = getSelectedValues();
        // 判断哪个的长度
        let obj = (keys.length > values.length ? keys : values);
        obj.forEach(function (index) {
            if (values.indexOf(index) < 0) {
                bindCheckedToSubOptions(data[index], index);
                deleteSelectedValues([index])
                delete data[index];
            } else if (!data[index]) {
                data[index] = {
                    column: options_cache_{{$unique_key}}[index].column,
                    label: options_cache_{{$unique_key}}[index].label,
                    sub: {},
                }
            }
        });
        options_checked_{{$unique_key}} = data;
        updateChecked();
    }


</script>

