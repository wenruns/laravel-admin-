<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/18
 * Time: 10:56
 */

namespace App\Admin\Extensions\Form;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Encore\Admin\Form\Field;

class MultiLevelLinkage extends Field
{

    /**
     * Other key for many-to-many relation.
     *
     * @var string
     */
    protected $otherKey;

    protected $view = 'admin.form.multilevel';


    protected $all_subs = [];


    protected $unique_key = '';

    /**
     * 子字段
     * @var array
     */
    protected $subColumns = [];

    /**
     * 子选项
     * @var array
     */
    protected $subOptions = [];

    /**
     * 当前级字段名称
     * @var string
     */
    protected $current_column = '';

    /**
     * 别名映射
     * @var array
     */
    protected $column_alias_map = [];

    protected $request_url = '';

    protected $checked_values = [];

    protected $level = '';


    /**
     * @var array
     */
    protected static $css = [
        '/vendor/laravel-admin/AdminLTE/plugins/select2/select2.min.css',
    ];

    /**
     * @var array
     */
    protected static $js = [
        '/vendor/laravel-admin/AdminLTE/plugins/select2/select2.full.min.js',
    ];

    /**
     * @var array
     */
    protected $config = [];

    public function __construct(string $column = '', array $arguments = [])
    {
        $this->unique_key = mt_rand(1000, 9999);
        parent::__construct($column, $arguments);
    }

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this|mixed
     */
    public function options($options = [])
    {
        // remote options
        if (is_string($options)) {
            // reload selected
            if (class_exists($options) && in_array(Model::class, class_parents($options))) {
                return $this->model(...func_get_args());
            }

            return $this->loadRemoteOptions(...func_get_args());
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        if (is_callable($options)) {
            $this->options = $options;
        } else {
            $this->options = (array)$options;
        }

        return $this;
    }

    /**
     * Load options from current selected resource(s).
     *
     * @param string $model
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function model($model, $idField = 'id', $textField = 'name')
    {
        if (!class_exists($model)
            || !in_array(Model::class, class_parents($model))
        ) {
            throw new \InvalidArgumentException("[$model] must be a valid model class");
        }

        $this->options = function ($value) use ($model, $idField, $textField) {
            if (empty($value)) {
                return [];
            }

            $resources = [];

            if (is_array($value)) {
                if (Arr::isAssoc($value)) {
                    $resources[] = Arr::get($value, $idField);
                } else {
                    $resources = array_column($value, $idField);
                }
            } else {
                $resources[] = $value;
            }

            return $model::find($resources)->pluck($textField, $idField)->toArray();
        };

        return $this;
    }

    /**
     * Load options from remote.
     *
     * @param string $url
     * @param array $parameters
     * @param array $options
     *
     * @return $this
     */
    protected function loadRemoteOptions($url, $parameters = [], $options = [])
    {
        $ajaxOptions = [
            'url' => $url . '?' . http_build_query($parameters),
        ];
        $configs = array_merge([
            'allowClear' => true,
            'placeholder' => [
                'id' => '',
                'text' => trans('admin.choose'),
            ],
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);

        $ajaxOptions = json_encode(array_merge($ajaxOptions, $options));

        $this->script = <<<EOT

$.ajax($ajaxOptions).done(function(data) {

  $("{$this->getElementClassSelector()}").each(function(index, element) {
      $(element).select2({
        data: data,
        $configs
      });
      var value = $(element).data('value') + '';
      if (value) {
        value = value.split(',');
        $(element).select2('val', value);
      }
  });
});

EOT;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function readOnly()
    {
        //移除特定字段名称,增加MultipleSelect的修订
        //没有特定字段名可以使多个readonly的JS代码片段被Admin::script的array_unique精简代码
        $script = <<<'EOT'
$("form select").on("select2:opening", function (e) {
    if($(this).attr('readonly') || $(this).is(':hidden')){
    e.preventDefault();
    }
});
$(document).ready(function(){
    $('select').each(function(){
        if($(this).is('[readonly]')){
            $(this).closest('.form-group').find('span.select2-selection__choice__remove').first().remove();
            $(this).closest('.form-group').find('li.select2-search').first().remove();
            $(this).closest('.form-group').find('span.select2-selection__clear').first().remove();
        }
    });
});
EOT;
        Admin::script($script);

        return parent::readOnly();
    }

    /**
     * Get other key for this many-to-many relation.
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getOtherKey()
    {
        if ($this->otherKey) {
            return $this->otherKey;
        }

        if (is_callable([$this->form->model(), $this->column]) &&
            ($relation = $this->form->model()->{$this->column}()) instanceof BelongsToMany
        ) {
            /* @var BelongsToMany $relation */
            $fullKey = $relation->getQualifiedRelatedPivotKeyName();
            $fullKeyArray = explode('.', $fullKey);

            return $this->otherKey = end($fullKeyArray);
        }

        throw new \Exception('Column of this field must be a `BelongsToMany` relation.');
    }

    /**
     * {@inheritdoc}
     */
    public function fill($data)
    {
        if ($this->form && $this->form->shouldSnakeAttributes()) {
            $key = Str::snake($this->column);
        } else {
            $key = $this->column;
        }

        $relations = Arr::get($data, $key);

        if (is_string($relations)) {
            $this->value = explode(',', $relations);
        }

        if (!is_array($relations)) {
            return;
        }

        $first = current($relations);

        if (is_null($first)) {
            $this->value = null;

            // MultipleSelect value store as an ont-to-many relationship.
        } elseif (is_array($first)) {
            foreach ($relations as $relation) {
                $this->value[] = Arr::get($relation, "pivot.{$this->getOtherKey()}");
            }

            // MultipleSelect value store as a column.
        } else {
            $this->value = $relations;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginal($data)
    {
        $relations = Arr::get($data, $this->column);

        if (is_string($relations)) {
            $this->original = explode(',', $relations);
        }

        if (!is_array($relations)) {
            return;
        }

        $first = current($relations);

        if (is_null($first)) {
            $this->original = null;

            // MultipleSelect value store as an ont-to-many relationship.
        } elseif (is_array($first)) {
            foreach ($relations as $relation) {
                $this->original[] = Arr::get($relation, "pivot.{$this->getOtherKey()}");
            }

            // MultipleSelect value store as a column.
        } else {
            $this->original = $relations;
        }
    }

    public function prepare($value)
    {
        $value = (array)$value;

        return array_filter($value, 'strlen');
    }

    public function requestUrl($url)
    {
        $this->request_url = $url;
        return $this;
    }

    /**
     * 设置子选项字段和请求路由
     * @param $column
     * @param $options
     * @param int $n
     * @return $this
     */
    public function subOptions($column, $options, $n = 1)
    {
        if (empty($this->current_column)) {
            $this->current_column = $this->makeAlias();
            $this->column_alias_map[$this->current_column] = $this->column;
        }
        $alias_name = $this->makeAlias();
        $this->column_alias_map[$alias_name] = $column;
        $this->subColumns[$this->current_column] = $alias_name;
        $this->subOptions[$alias_name] = $options;
        $this->current_column = $alias_name;
        $this->form->multipleSelect($column)->display = false;

        if ($n > 1) {
            for ($m = 1; $m < $n; $m++) {
                $new_alias_name = $this->makeAlias();
                $this->subColumns[$this->current_column] = $new_alias_name;
                $this->column_alias_map[$new_alias_name] = &$this->column_alias_map[$this->current_column];
                $this->subOptions[$new_alias_name] = &$this->subOptions[$this->current_column];
                $this->current_column = $new_alias_name;
            }
        }

        return $this;
    }

    /**
     * 随机生成唯一别名
     * @param int $len
     * @return string
     */
    protected function makeAlias($len = 5)
    {
        $strs = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $name = md5(substr(str_shuffle($strs), mt_rand(0, strlen($strs) - $len), $len));
        if (isset($this->column_alias_map[$name])) {
            return $this->makeAlias();
        }
        return $name;
    }


    /**
     *
     * {@inheritdoc}
     */
    public function render()
    {
//        dd($this->subOptions, $this->subColumns, $this->column_alias_map);

        $configs = array_merge([
            'allowClear' => true,
            'placeholder' => [
                'id' => '',
                'text' => $this->label,
            ],
        ], $this->config);

        $configs = json_encode($configs);

        if (empty($this->script)) {
            $this->script = "$(\"{$this->getElementClassSelector()}\").select2($configs);";
        }

        if ($this->options instanceof \Closure) {
            if ($this->form) {
                $this->options = $this->options->bindTo($this->form->model());
            }

            $this->options(call_user_func($this->options, $this->value, $this));
        }

        $this->options = array_filter($this->options, 'strlen');


//        dd($this->defaultCheckedValue(), $this->checked_values);
        $this->addVariables([
            'options' => $this->options,
            'unique_key' => $this->unique_key,
            'sub_columns' => json_encode($this->subColumns),
            'sub_options' => json_encode($this->subOptions),
            'alias_map' => json_encode($this->column_alias_map),
            'last_level_column' => $this->current_column,
            'default_checked_value' => $this->defaultCheckedValue(),
            'select_alias' => key($this->subColumns),
            'request_url' => $this->request_url,
            'checked_values' => json_encode($this->checked_values),
        ]);

        $this->attribute('data-value', implode(',', (array)$this->value()));

        return parent::render();
    }


    /**
     * 填充默认值
     * @return false|string
     */
    protected function defaultCheckedValue()
    {
        try {
            if (empty($this->value)) {
                return json_encode([]);
            }
            // 第一级
            $alias_name = key($this->subColumns);
            $res = [];
            $options = $this->subOptions[$this->subColumns[$alias_name]];
            $data = array_column($this->getData($this->column), $options['parent_label'], $options['parent_field']);
            $next_alias_name = isset($this->subColumns[$alias_name]) ? $this->subColumns[$alias_name] : '';
            foreach ($this->value as $key) {
                $this->level = $key;
                $sub = $this->makeSub($key, $next_alias_name, $options);
                $res[$key] = [
                    'column' => $this->column,
                    'label' => $data[$key],
                    'sub' => $sub,
                ];
                if (empty($sub)) {
                    $this->checked_values[] = $this->level;
                }
            }
//            dd($res);
            return json_encode($res);
        } catch (\Exception $e) {
//            dd($this->getData($this->column), $this->column, $this->value, $options, $data, $e);
        }
    }

    /**
     * 填充子选项
     * @param $key
     * @param $alias_name
     * @param $options
     * @param string $index
     * @return array
     */
    protected function makeSub($key, $alias_name, $options, $index = '')
    {
        if (empty($alias_name)) {
            return [];
        }
        $res = [];
        // 获取真实字段名称
        $real_column = $this->column_alias_map[$alias_name];
        // 获取下级字段别名
        $next_alias_name = isset($this->subColumns[$alias_name]) ? $this->subColumns[$alias_name] : $alias_name;
        // 获取下级参数
        $next_options = empty($next_alias_name) ? '' : $this->subOptions[$next_alias_name];
        // 获取下级的父级字段
        $field = empty($next_options) ? '' : $next_options['parent_field'];
        // 获取数据
        $data = $this->getData($real_column);
        // 保存下级
        $allSubs = [];
        foreach ($data as $k => $item) {
            $dex = (empty($index) ? '' : $index . '_') . $key . '_' . $item[$options['sub_value']];
            if ($item[$options['sub_field']] != $key || isset($this->all_subs[$dex])) {
                continue;
            }
            // 保存已勾选的选项
            $level = $this->level;
            $this->level .= '-' . $item[$options['sub_value']];

            $sub = empty($next_alias_name) ? [] : self::makeSub($item[$field], $next_alias_name, $next_options, empty($index) ? $key : $index . '_' . $key);
            $allSubs = array_merge($allSubs, $sub);
            // 判断当前数据是否为下级数据
            if (isset($allSubs[$item[$options['sub_value']]])) {
                $this->level = $level;
                continue;
            }
            $res[$item[$options['sub_value']]] = [
                'column' => $real_column,
                'label' => $item[$options['sub_label']],
                'sub' => $sub,
            ];
            $this->all_subs[$dex] = true;
            $this->checked_values[] = $this->level;
            $this->level = $level;
        }
        return $res;
    }


    protected function getData($index)
    {
        $index = explode('.', $index);
        $data = $this->form->model()->toArray();
        if (empty($data)) {
            return [];
        }
        foreach ($index as $dex) {
            $data = $data[$dex];
        }
        return $data;
    }

}