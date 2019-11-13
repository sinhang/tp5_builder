<?php


namespace app\tools\service;


class Service extends Init
{

    public function create()
    {
        $code   = sprintf('%s}', str_repeat(self::CODE_ROW_RIGHT, 2));

        return $this->put($code);
    }

}