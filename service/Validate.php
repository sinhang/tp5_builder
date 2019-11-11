<?php


namespace app\tools\service;


class Validate extends Init
{

    protected $suffix = '', $parentClass = '\\think\\Validate';

    public function create()
    {

        $code   = sprintf("%s%s%s}",
            $this->generateRule(),
            $this->generateScene(),
            $this->generateField()
        );

        return $this->put($code);

    }

    protected function generateRule()
    {
        $code   = $this->generateCodeRow("protected ".'$rule'." = [");

        if (!empty($this->data['validateFields'])) {
            foreach ($this->data['validateFields'] as $v) {
                $code   .= $this->generateCodeRow(sprintf("'%s'\t=>\t'%s',", $v['field'], $v['rules']), true);
            }
        }

        $code   .= $this->generateCodeRow("];", false, true);
        return $code;
    }

    protected function generateField()
    {
        $code   = $this->generateCodeRow("protected ".'$field'." = [");

        if (!empty($this->data['validateFields'])) {
            foreach ($this->data['validateFields'] as $v) {
                $code   .= $this->generateCodeRow(sprintf("'%s'\t=>\t'{%s_%s}',", $v['field'], $this->getNoPrefixTableName($this->data['table']), $v['field']), true);
            }
        }

        $code   .= $this->generateCodeRow("];", false, true);

        return $code;
    }

    protected function generateScene()
    {

        $code   = $this->generateCodeRow("protected ".'$scene'." = [");

        if (!empty($this->data['scene'])) {
            foreach ($this->data['scene'] as $v) {
                $code   .= $this->generateCodeRow(sprintf("'%s'\t=>\t%s", $v['scene'], $this->arrToStrArr($v['fields'])), true);
            }
        }
        $code   .= $this->generateCodeRow("];", false, true);

        return $code;
    }

}