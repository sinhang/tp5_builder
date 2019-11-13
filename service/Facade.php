<?php


namespace app\tools\service;


class Facade extends Init
{

    protected $parentClass = '\\think\\Facade';

    public function create()
    {
        $code   = sprintf('%s}', $this->generateMethod());

        return $this->put($code);
    }

    protected function generateMethod()
    {
        $code   = $this->generateCodeRow('protected static function getFacadeClass() { ');

        $code   .= $this->generateCodeRow(sprintf('return %s%s::class;', $this->data['model_namespace'], $this->getFormatTableName()), true);

        $code   .= $this->generateCodeRow('}', false, true);

        return $code;
    }

}