<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/22
 * Time: 14:54
 */

namespace App\Admin\Extensions\Buttons;


use Encore\Admin\Facades\Admin;

class Button
{

    protected $_hadCreateBtn = false;

    protected $_unique = '';

    protected $_spacer = '';

    protected $_buttons = [];

    protected $_clickEvents = [];

    protected $_n = 1;

    public function __construct($buttons, $spacer = '')
    {
        $this->_buttons = $buttons;
        $this->_spacer = $spacer;
        $this->_unique = mt_rand(1000, 9999);
    }

    protected function setScript()
    {
        $unique = $this->_unique;
        $clickEvents = json_encode($this->_clickEvents);
        $script = <<<SCRIPT
$(".wen-button-$unique").click(function(e){
    var clickEvents = $clickEvents;
    var func = clickEvents[e.currentTarget.dataset.sign];
    console.log(func);
    if(func){
        func = "var f = " + func
        eval(func);
        f(e);
    }
});
SCRIPT;
        Admin::script($script);
    }

    protected function setHtml()
    {
        $html = '';
        if (isset($this->_buttons[0])) {
            foreach ($this->_buttons as $key => $item) {
                $html .= $this->createBtn($item) . $this->_spacer;
            }
            $html = rtrim($html, $this->_spacer);
        } else {
            $html .= $this->createBtn($this->_buttons);
        }

        if ($this->_hadCreateBtn) {
            $this->setScript();
        }
        return $html;
    }

    protected function createBtn($item)
    {
        if (!$this->checkShow($item)) {
            return '';
        }
        $this->_hadCreateBtn = true;
        $item = $this->format($item);
        $class = $item['class'];
        $style = $item['style'];
        $url = $item['url'];
        $buttonText = $item['buttonText'];
        $unique = $this->_unique;
        $data = is_array($item['data']) ? json_encode($item['data']) : $item['data'];
        $sign = md5($buttonText);
        $attributes = $item['attributes'];
        return <<<HTML
<a class="wen-button-$unique $class"  $attributes  style="$style"  href="$url" data-sign="$sign" data-url="$url" data-text="$buttonText" data-datas="$data">$buttonText</a>
HTML;
    }

    protected function format($item)
    {
        // 按钮类
        isset($item['class']) ? '' : $item['class'] = ('btn ' . (isset($item['buttonType']) ? 'btn-' . $item['buttonType'] : 'btn-xs') . (isset($item['buttonStyle']) ? 'btn-' . $item['buttonStyle'] : 'btn-primary'));
        // 按钮样式
        isset($item['style']) ? '' : $item['style'] = '';
        // 按钮url
        isset($item['url']) ? '' : $item['url'] = '#';
        // 按钮文本
        if (!isset($item['buttonText'])) {
            $item['buttonText'] = 'button' . $this->_n;
            $this->_n++;
        }
        // 附加数据
        isset($item['data']) ? '' : $item['data'] = '';
        // 自定义按钮属性
        if (isset($item['attributes'])) {
            if (is_array($item['attributes'])) {
                $attributes = '';
                foreach ($item['attributes'] as $attrName => $attrValue) {
                    $attributes .= $attrName . '="' . $attrValue . '" ';
                }
                $item['attributes'] = $attributes;
            }
        } else {
            $item['attributes'] = '';
        }
        // 按钮点击触发事件
        if (isset($item['clickEvent'])) {
            $this->_clickEvents[md5($item['buttonText'])] = $this->compressHtml($item['clickEvent']);
        }
        return $item;
    }

    protected function checkShow($item)
    {
        return isset($item['show']) ? $item['show'] : true;
    }

    public function render()
    {
        return $this->setHtml();
    }

    /** 压缩html */
    protected function compressHtml($string)
    {
        return ltrim(rtrim(preg_replace(array("/> *([^ ]*) *</", "//", "'/\*[^*]*\*/'", "/\r\n/", "/\n/", "/\t/", '/>[ ]+</'),
            array(">\\1<", '', '', '', '', '', '><'), $string)));
    }
}