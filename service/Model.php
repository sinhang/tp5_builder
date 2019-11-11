<?php


namespace app\tools\service;


class Model extends Init
{
    //  后缀，继承上级类名
    protected $suffix = 'Model', $parentClass = '\\think\\Model', $use = 'use think\model\concern\SoftDelete;', $trait = 'use SoftDelete;';

    /**
     * 创建文件
     */
    public function create()
    {
        // TODO: Implement create() method.

        $code   = sprintf("%s%s%s%s%s}",
            $this->generateAttr(),
            $this->generateAppendFunction(),
            $this->generateSetFunction(),
            $this->generateGetFunction(),
            $this->generateRelation());

        return $this->put($code);

    }

    /**
     * 生成文件属性
     */
    protected function generateAttr()
    {
        return sprintf('\tprotected $table = \'%s\';
    protected $pk = \'%s\';
    protected $deleteTime = \'%s\';
    protected $resultSetType = \'collection\';
    protected $autoWriteTimestamp = false;
    protected $createTime = \' %s\';
    protected $updateTime = \' %s\';
    protected $dateFormat = \'Y-m-d H:i:s\';
    protected $append = %s;
    protected $readonly  = %s;
    protected $auto = %s;
    protected $insert = %s;
    protected $update = %s;%s',
            $this->getTableName(),
            $this->data['pk'],
            $this->data['delete_time'],
            $this->data['create_time'],
            $this->data['update_time'],
            $this->arrToStrArr($this->getAppendField(), ''),
            $this->arrToStrArr($this->getAttrByName('readonly'), ''),
            $this->arrToStrArr($this->getAttrByName('auto'), ''),
            $this->arrToStrArr($this->getAttrByName('insert'), ''),
            $this->arrToStrArr($this->getAttrByName('update'), ''),
            str_repeat(self::CODE_ROW_RIGHT, 2));
    }

    /**
     * 生成append方法
     */
    protected function generateAppendFunction()
    {
        $code   = '';

        if (!empty($this->data['append'])) {
            foreach ($this->data['append'] as $v) {
                $name   = $this->formatName($v['name']);
                $code   .= $this->generateCodeRow('protected function get'.$name.'TextAttr($value, $data) {');
                $code   .= $this->generateCodeRow($v['body'], true);
                $code   .= $this->generateCodeRow('}', false, true);
            }
        }

        return $code;
    }

    /**
     * 生成set方法
     */
    protected function generateSetFunction()
    {
        $code   = '';

        if (!empty($this->data['set'])) {
            foreach ($this->data['set'] as $v) {
                $name   = $this->formatName($v['name']);
                $code   .= $this->generateCodeRow('protected function set'.$name.'Attr($value, $data) {');
                $code   .= $this->generateCodeRow($v['body'], true);
                $code   .= $this->generateCodeRow('}', false, true);
            }
        }

        return $code;

    }

    /**
     * 生成get方法
     */
    protected function generateGetFunction()
    {
        $code   = '';

        if (!empty($this->data['get'])) {
            foreach ($this->data['get'] as $v) {
                $name   = $this->formatName($v['name']);
                $code   .= $this->generateCodeRow('protected function get'.$name.'Attr($value, $data) {');
                $code   .= $this->generateCodeRow($v['body'], true);
                $code   .= $this->generateCodeRow('}', false, true);
            }
        }

        return $code;
    }

    /**
     * 生成关联模型
     */
    protected function generateRelation()
    {
        $code   = '';

        if (!empty($this->data['relation'])) {
            foreach ($this->data['relation'] as $v) {
                $name   = $this->formatName($this->getNoPrefixTableName($v['table']));
//                $table  = $this->getNoPrefixTableName($this->getTableName());
                $code   .= $this->generateCodeRow("protected function {$name}() {");
                $code   .= $this->generateCodeRow(
                    sprintf('return $this->%s(%s, \'%s\', \'%s\')%s%s->field(\'%s\');',
                        $v['relation'],
                        "{$this->data['namespace']}\\{$name}{$this->suffix}::class",
                        $v['foreignKey'], $v['localKey'],
                        self::CODE_ROW_RIGHT,
                        str_repeat(self::CODE_ROW_LEFT, 3),
                        !empty($v['selectFields']) ? implode(',', $v['selectFields']) : '*'),
                    true);
                $code   .= $this->generateCodeRow('}', false, true);
            }
        }

        return $code;
    }

    /**
     * 获取append字段
     */
    protected function getAppendField()
    {
        $fields = [];
        if (!empty($this->data['append'])) {
            foreach ($this->data['append'] as $v) {
                array_push($fields, "{$v['name']}_text");
            }
        }

        return $fields;
    }

    /**
     * 获取属性字段
     * @param $name string [readonly, auto, insert, update]
     * @return string
     */
    protected function getAttrByName($name)
    {
        $fields = [];
        if (!empty($this->data['attrs'])) {
            $tmp    = array_filter($this->data['attrs'], function ($v) use ($name) {
                return $v['title'] == $name;
            });
            $tmpArr = array_values($tmp);
            $fields = $tmpArr[0]['fields'];
        }

        return $fields;
    }

}