<?php

namespace Rule;

/** 验证码生成并发送 */
class Ver_code implements IRule
{
    public static function run(?array $args = [], ?\Good_wechat $wechat): string
    {
        $ver_code = rand(100000, 999999);
        $sql = "";
        return self::init_table($wechat);
    }
    /** 初始化数据表 */
    public static function init_table(?\Good_wechat $wechat)
    {
        $table = $wechat->mysql_config['table']['ver_code'];
        return $table;
    }
}
