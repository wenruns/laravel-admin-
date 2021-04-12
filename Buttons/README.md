按钮管理类
======================
    严格来说，这个Button类并不属于laravel-admin插件，允许在任何项目中引入使用。
    
1、使用方法
----------------------
    复制文件到指定目录下即可。（注意要根据本身项目调整类的命名空间）
    
2、案例
----------------------
    
    (new Button([
                    [
                        'buttonText' => '', // 按钮文本
                        'buttonType' => '', // 按钮类型，参考bootstrap的btn-xs
                        'buttonStyle' => '', // 按钮风格，参考bootstrap的btn-primary
                        'url' => '', // 按钮url
                        'class' => '', // 按钮class，默认btn btn-xs btn-primary
                        'style'=>'', // 自定义样式
                        'data' => '', // 附加数据
                        'attributes' => [
                            "target"=>"_blank",
                        ], // 自定义属性
                        'clickEvent' => 'function(e){
                            console.log(e);
                        }',
                    ],[
                        'buttonText' => '',
                        'url' => '',
                    ]
                ], '|'))->render()
                
3、说明
-----------------------
    1、案例是创建多个按钮，也可以传一个一维数组，创建单个按钮。
    2、构造函数的第二个参数“|”是按钮之间的间隔符号，选填。
    3、其他请看注释说明。