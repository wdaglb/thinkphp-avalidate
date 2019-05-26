<?php


namespace ke;


class Parse
{

    /**
     * 下划线转驼峰
     * @param $str
     * @return string|string[]|null
     */
    public static function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i',function($matches){
            return strtoupper($matches[2]);
        },$str);
        return $str;
    }


    /**
     * 驼峰转下划线
     * @param $str
     * @return string|string[]|null
     */
    public static function humpToLine($str)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $str));;
    }


}
