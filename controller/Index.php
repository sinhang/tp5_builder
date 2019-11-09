<?php


namespace app\tools\controller;


use app\tools\service\Model;
use app\tools\service\Validate;
use think\Db;
use think\Exception;
use think\Response;

class Index
{

    protected $ret  = [
        'code'  => 1,
        'msg'   => '操作成功',
        'data'  => []
    ], $tables = [], $rules = [];

    public function __construct()
    {
        $db = config('database.database');
        $this->tables = Db::query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$db}'");

        if ($this->tables) {
            foreach ($this->tables as &$v) {
                $v['fields']  = Db::query("show FULL columns from {$v['TABLE_NAME']} from {$db}");
                if ($v['fields']) {
                    foreach ($v['fields'] as &$val) {
                        $val['rules']       = $this->rules();
                        $val['ruleValue']   = '';
                    }
                }
            }
        }
//dump($this->tables[0]['fields'][0]);
        //  验证规则
        $this->rules    = $this->rules();
    }

    public function index()
    {
        return view('', [
            'tables' => json_encode($this->tables, JSON_UNESCAPED_UNICODE),
            'rules' => json_encode($this->rules, JSON_UNESCAPED_UNICODE)]);
    }


    public function validate()
    {
        try {
            if (!request()->post()) throw new Exception('非法操作');

            Validate::instance(request()->param())->create();

        } catch (Exception $e) {
            $this->ret  = [
                'code'  => $e->getCode(),
                'msg'   => $e->getMessage()
            ];
        }
        Response::create($this->ret, 'json')->send();
    }

    public function model()
    {
        try {
            if (!request()->post()) throw new Exception('非法操作');
            Model::instance(request()->param())->create();
        } catch (Exception $e) {
            $this->ret  = [
                'code'  => $e->getCode(),
                'msg'   => $e->getMessage(),
            ];
        }
        Response::create($this->ret, 'json')->send();
    }

    protected function rules()
    {
        return [
            [
                'rule'  => 'require',
                'title' => '必填',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段必须'
            ],
            [
                'rule'  => 'number',
                'title' => '纯数字',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为纯数字（采用ctype_digit验证，不包含负数和小数点）'
            ],
            [
                'rule'  => 'integer',
                'title' => '整数',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为整数（采用filter_var验证）'
            ],
            [
                'rule'  => 'float',
                'title' => '浮点',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为浮点数字（采用filter_var验证）'
            ],
            [
                'rule'  => 'boolean',
                'title' => '布尔',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为布尔值（采用filter_var验证）'
            ],
            [
                'rule'  => 'email',
                'title' => 'email',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为email地址（采用filter_var验证）'
            ],
            [
                'rule'  => 'array',
                'title' => '数组',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为数组'
            ],
            [
                'rule'  => 'accepted',
                'title' => '服务条款',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段是否为为 yes, on, 或是 1。这在确认"服务条款"是否同意时很有用'
            ],
            [
                'rule'  => 'date',
                'title' => '日期',
                'type'  => 'checkbox',
                'tips'  => '验证值是否为有效的日期'
            ],
            [
                'rule'  => 'alpha',
                'title' => '纯字母',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为纯字母'
            ],
            [
                'rule'  => 'alphaNum',
                'title' => '字母及数字',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为字母和数字'
            ],
            [
                'rule'  => 'alphaDash',
                'title' => '字母数字-_',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为字母和数字，下划线_及破折号-'
            ],
            [
                'rule'  => 'chs',
                'title' => '纯汉字',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是汉字'
            ],
            [
                'rule'  => 'chsAlpha',
                'title' => '汉字及字母',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是汉字、字母'
            ],
            [
                'rule'  => 'chsAlphaNum',
                'title' => '汉字字母及数字',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是汉字、字母和数字'
            ],
            [
                'rule'  => 'cntrl',
                'title' => '换行缩进空格',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是控制字符（换行、缩进、空格）'
            ],
            [
                'rule'  => 'graph',
                'title' => '非空格',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是可打印字符（空格除外）'
            ],
            [
                'rule'  => 'print',
                'title' => '可打印字符',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是可打印字符（包括空格）'
            ],
            [
                'rule'  => 'lower',
                'title' => '小写字符',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是小写字符'
            ],
            [
                'rule'  => 'upper',
                'title' => '大写字符',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是大写字符'
            ],
            [
                'rule'  => 'space',
                'title' => '空白字符',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是空白字符（包括缩进，垂直制表符，换行符，回车和换页字符'
            ],
            [
                'rule'  => 'xdigit',
                'title' => '十六进制',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值只能是十六进制字符串'
            ],
            [
                'rule'  => 'activeUrl',
                'title' => '域名或IP',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为有效的域名或者IP'
            ],
            [
                'rule'  => 'url',
                'title' => 'URL',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为有效的URL地址（采用filter_var验证）'
            ],
            [
                'rule'  => 'ip',
                'title' => 'IP',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为有效的IP地址（采用filter_var验证）'
            ],
            [
                'rule'  => 'dateFormat:format',
                'title' => '指定格式的日期',
                'type'  => 'input',
                'tips'  => '验证某个字段的值是否为指定格式的日期[2019-11-07]'
            ],
            [
                'rule'  => 'mobile',
                'title' => '手机号码',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为有效的手机'
            ],
            [
                'rule'  => 'idCard',
                'title' => '身份证号码',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为有效的身份证格式'
            ],
            [
                'rule'  => 'macAddr',
                'title' => 'MAC地址',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为有效的MAC地址'
            ],
            [
                'rule'  => 'zip',
                'title' => '邮政编码',
                'type'  => 'checkbox',
                'tips'  => '验证某个字段的值是否为有效的邮政编码'
            ],
            [
                'rule'  => 'in',
                'title' => 'IN',
                'type'  => 'input',
                'tips'  => '验证某个字段的值是否在某个范围:1,2,3'
            ],
            [
                'rule'  => 'notIn',
                'title' => 'not In',
                'type'  => 'input',
                'tips'  => '验证某个字段的值不在某个范围'
            ],
            [
                'rule'  => 'between',
                'title' => 'between',
                'type'  => 'input',
                'tips'  => '验证某个字段的值是否在某个区间:1,10'
            ],
            [
                'rule'  => 'notBetween',
                'title' => 'not Between',
                'type'  => 'input',
                'tips'  => '验证某个字段的值不在某个范围:1,10'
            ],
            [
                'rule'  => 'length',
                'title' => 'length',
                'type'  => 'input',
                'tips'  => '验证某个字段的值的长度是否在某个范围:5,50 or 5'
            ],
            [
                'rule'  => 'max',
                'title' => 'max',
                'type'  => 'input',
                'tips'  => '如果验证的数据是数组，则判断数组的长度。如果验证的数据是File对象，则判断文件的大小。:25'
            ],
            [
                'rule'  => 'min',
                'title' => 'min',
                'type'  => 'input',
                'tips'  => '如果验证的数据是数组，则判断数组的长度。如果验证的数据是File对象，则判断文件的大小。:5'
            ],
            [
                'rule'  => 'after',
                'title' => '指定日期之后',
                'type'  => 'input',
                'tips'  => '验证某个字段的值是否在某个日期之后: 2016-3-18'
            ],
            [
                'rule'  => 'before',
                'title' => '指定日期之前',
                'type'  => 'input',
                'tips'  => '验证某个字段的值是否在某个日期之前:2019-12-30'
            ],
            [
                'rule'  => 'expire',
                'title' => '指定日期内',
                'type'  => 'input',
                'tips'  => '验证当前操作（注意不是某个值）是否在某个有效日期之内: 2016-2-1,2016-10-01'
            ],
            [
                'rule'  => 'allowIp',
                'title' => 'allowIp',
                'type'  => 'input',
                'tips'  => '验证当前请求的IP是否在某个范围: 114.45.4.55,多个IP用逗号分隔'
            ],
            [
                'rule'  => 'denyIp',
                'title' => 'denyIp',
                'type'  => 'input',
                'tips'  => '验证当前请求的IP是否禁止访问: 114.45.4.55,多个IP用逗号分隔'
            ],
            [
                'rule'  => 'confirm',
                'title' => '两个字段相同值',
                'type'  => 'input',
                'tips'  => '验证某个字段是否和另外一个字段的值一致: confirm:password'
            ],
            [
                'rule'  => 'different',
                'title' => '两个字段不同值',
                'type'  => 'input',
                'tips'  => '验证某个字段是否和另外一个字段的值不一致'
            ],
            [
                'rule'  => 'eq',
                'title' => '等于',
                'type'  => 'input',
                'tips'  => '验证是否等于某个值: 100'
            ],
            [
                'rule'  => 'egt',
                'title' => '大于等于',
                'type'  => 'input',
                'tips'  => '验证是否大于等于某个值'
            ],
            [
                'rule'  => 'gt',
                'title' => '大于',
                'type'  => 'input',
                'tips'  => '验证是否大于某个值'
            ],
            [
                'rule'  => 'elt',
                'title' => '小于等于',
                'type'  => 'input',
                'tips'  => '验证是否小于等于某个值'
            ],
            [
                'rule'  => 'lt',
                'title' => '小于',
                'type'  => 'input',
                'tips'  => '验证是否小于某个值'
            ],
            [
                'rule'  => 'filter',
                'title' => 'filter var',
                'type'  => 'input',
                'tips'  => '支持使用filter_var进行验证'
            ],
            [
                'rule'  => 'regex',
                'title' => '正则',
                'type'  => 'input',
                'tips'  => '支持直接使用正则验证: regex:\d{6}'
            ],
            [
                'rule'  => 'file',
                'title' => '文件验证',
                'type'  => 'checkbox',
                'tips'  => '验证是否是一个上传文件'
            ],
            [
                'rule'  => 'image',
                'title' => '图片验证',
                'type'  => 'input',
                'tips'  => '验证是否是一个图像文件，width height和type都是可选，width和height必须同时定义。image:width,height,type'
            ],
            [
                'rule'  => 'fileExt',
                'title' => '文件后缀',
                'type'  => 'input',
                'tips'  => '验证上传文件后缀: .jpg'
            ],
            [
                'rule'  => 'fileMime',
                'title' => '文件类型',
                'type'  => 'input',
                'tips'  => '验证上传文件类型'
            ],
            [
                'rule'  => 'fileSize',
                'title' => '文件大小',
                'type'  => 'input',
                'tips'  => '验证上传文件大小'
            ],
            [
                'rule'  => 'unique',
                'title' => '唯一验证',
                'type'  => 'input',
                'tips'  => '验证当前请求的字段值是否为唯一的: table,field,except,pk'
            ],
            [
                'rule'  => 'requireIf',
                'title' => 'requireIf',
                'type'  => 'input',
                'tips'  => '验证某个字段的值等于某个值的时候必须,当account的值等于1的时候 password必须requireIf:account,1'
            ],
            [
                'rule'  => 'requireWith',
                'title' => 'requireWith',
                'type'  => 'input',
                'tips'  => '验证某个字段有值的时候必须,当account有值的时候password字段必须,requireWith:account'
            ],
            [
                'rule'  => 'requireCallback',
                'title' => 'requireCallback',
                'type'  => 'input',
                'tips'  => '验证当某个callable为真的时候字段必须,使用check_require方法检查是否需要验证age字段必须,requireCallback:check_require|number'
            ]
        ];
    }
}