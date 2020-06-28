<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/16
 * Time: 9:51
 */

namespace App\Admin\Extensions\Form;


use App\Utils\HtmlHelp;
use BaUcenter\Admin;
use Encore\Admin\Form\Field;

class Input extends Field\Text
{

    protected $view = 'admin.form.input';

    protected $buttonText = '查询';

    protected $buttonAttributes = '';

    protected $buttonStyles = '';

    protected $buttonWidth = '15%';

    protected $className = 'attach-input-button';

    protected $clickEvent = 0;

    public function button($options = [])
    {
        isset($options['text']) && $this->buttonText = $options['text'];
        isset($options['attributes']) && $this->buttonAttributes = $this->makeAttributes($options['attributes']);
        isset($options['styles']) && $this->buttonStyles = $this->makeStyles($options['styles']);
        isset($options['clickEvent']) && $this->clickEvent = $options['clickEvent'];
        return $this;
    }

    protected function makeStyles($styles)
    {
        if (is_array($styles)) {
            $string = '';
            foreach ($styles as $key => $value) {
                if (strtolower($key) == 'width') {
                    $this->buttonWidth = $value;
                } else {
                    $string .= $key . ':' . $value . ';';
                }
            }
            return $string;
        }
        $styles = strtolower($styles);
        preg_match('/width\s*:(.*?);/', $styles, $result);
        if (empty($result)) {
            return $styles;
        }
        $styles = str_replace($result[0], '', $styles);
        $this->buttonWidth = trim($result[1]);
        return $styles;
    }

    protected function makeAttributes($attributes)
    {
        if (is_array($attributes)) {
            $string = '';
            foreach ($attributes as $key => $value) {
                $string .= $key . '="' . (is_array($value) ? json_encode($value) : $value) . '" ';
            }
            return $string;
        }
        return $attributes;
    }

    public function render()
    {
        $this->className = 'attach-input-button-' . mt_rand(0, 9999);
        $script = <<<SCRIPT
        document.querySelector('.{$this->className}').addEventListener('click',function(e){
            let clickEvent = $this->clickEvent;
            if(clickEvent){
                clickEvent(e);
            }
        });
SCRIPT;
        \Encore\Admin\Facades\Admin::script(HtmlHelp::compressHtml($script));
        $this->addVariables([
            'buttonText' => $this->buttonText,
            'buttonAttr' => $this->buttonAttributes,
            'buttonStyle' => $this->buttonStyles,
            'buttonWidth' => $this->buttonWidth,
            'buttonClass' => $this->className,
        ]);
        return parent::render();
    }

}