<?php


namespace app\tools\service;


use think\Exception;

abstract class Init
{
    //  文件扩展
    const EXT   = '.php';
    //  新建文件夹的权限
    const MODE  = '0777';
    //  tab
    const CODE_ROW_LEFT = '\t';
    //  换行
    const CODE_ROW_RIGHT = PHP_EOL;
    //  数据，后缀，继承class名称
    protected $data = [], $suffix = '', $parentClass = '', $use = '', $trait = '';
    //  单例
    public static $instance;

    /**
     * Init constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * 创建文件
     */
    abstract public function create();

    /**
     * 获取文件路径
     */
    protected function getFilePath()
    {
        $path   = sprintf("%s%s%s",
            APP_PATH,
            $this->data['filepath'],
            strripos($this->data['filepath'], '/') !== strlen($this->data['filepath']) -1 ? '/' : '');

        try {

            if (!is_dir($path)) {
                mkdir($path, self::MODE, true);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $path;
    }

    /**
     * 获取文件名称
     * @param $rename boolean 是否修改字母
     * @return string
     */
    protected function getFilename($rename = false)
    {
        return sprintf("%s%s%s%s%s", $this->getFilePath(), $this->getFormatTableName(), $this->suffix, ($rename ? date('YmdHis') : '') , self::EXT);
    }

    public function hasFile()
    {
        return file_exists(sprintf("%s%s%s%s", $this->getFilePath(), $this->getFormatTableName(), $this->suffix, self::EXT));
    }

    /**
     * 获取文件头部
     */
    protected function getFileHeader()
    {
        return sprintf('<?php%s/**%s * Date: %s%s */%snamespace %s;%s%s%sclass %s%s extends %s { %s%s%s',
            str_repeat(self::CODE_ROW_RIGHT, 1),
        self::CODE_ROW_RIGHT,
            date('Y-m-d H:i:s', time()),
            self::CODE_ROW_RIGHT,
            str_repeat(self::CODE_ROW_RIGHT, 3),
            $this->data['namespace'],
            str_repeat(self::CODE_ROW_RIGHT, 2),
            $this->use,
            str_repeat(self::CODE_ROW_RIGHT, 2),
            $this->getFormatTableName(),
            $this->suffix,
            $this->parentClass,
            str_repeat(self::CODE_ROW_RIGHT, 2),
            ($this->trait ? sprintf("\t%s", $this->trait) : ''),
            str_repeat(self::CODE_ROW_RIGHT, 2)
            );
    }

    /**
     * 获取表名
     */
    protected function getTableName()
    {
        return $this->data['table'];
    }

    /**
     * 获取无前缀表名
     */
    protected function getNoPrefixTableName($table)
    {
        return str_replace(config('database.prefix'), '', $table);
    }

    /**
     * 首字母大写
     */
    protected function ucFirst($text)
    {
        return ucfirst($text);
    }

    /**
     * 获取解析后的表名
     */
    protected function getFormatTableName()
    {
        return $this->formatName($this->getNoPrefixTableName($this->getTableName()));
    }

    /**
     * 生成一行代码
     */
    protected function generateCodeRow($str, $isInline = false, $isLast = false)
    {
        return sprintf("%s%s%s",
            str_repeat(self::CODE_ROW_LEFT, $isInline ? 2 : 1),
            $str,
            str_repeat(self::CODE_ROW_RIGHT, $isLast ? 3 : 1));
    }

    /**
     * 获取单例
     */
    public static function instance(array $data)
    {
        return new static($data);
    }

    /**
     * 数组转字符串数组
     */
    protected function arrToStrArr($arr, $last = ',', $start = '[', $end = ']')
    {

        if (empty($str)) {
            $str    = "{$start}{$end}";
        } else {
            $str    = "{$start}'";
            if (!empty($arr)) {
                $str .= implode("', '", $arr);
            }
            $str .= "'{$end}{$last}";
        }

        return $str;
    }

    /**
     * put代码至文件
     */
    protected function put($code)
    {
        //  如果存在则修改文件名字
        if ($this->hasFile()) {
            rename($this->getFilename(), $this->getFilename(true));
        }

        return file_put_contents($this->getFilename(), sprintf("%s%s", $this->getFileHeader(), str_replace(['\r\n', '\t'], [PHP_EOL, "\t"], $code)));
    }


    /**
     * 将数据表中的下划线转成空字符，并且首字母及下划线首字母大写
     */
    protected function formatName($name)
    {
        $arr    = explode('_', $name);
        $tmp    = '';
        foreach ($arr as $v) {
            $tmp    .= $this->ucFirst($v);
        }
        return $tmp;
    }
}