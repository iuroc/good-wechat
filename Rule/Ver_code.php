<?php

namespace Rule;

/** 验证码生成并发送 */
class Ver_code implements IRule
{
    /** 数据表名 */
    public static string $table;
    /** 验证码内容 */
    public static string $ver_code;
    public static function run(?array $args = [], ?\Good_wechat $wechat): string
    {
        self::$table = $wechat->mysql_config['table']['ver_code'];
        self::init_table($wechat);
        return self::$ver_code;
    }
    /** 初始化数据表 */
    public static function init_table(?\Good_wechat $wechat)
    {
        $table = self::$table;
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `ver_code` VARCHAR(20) COMMENT '验证码',
            `user_id` VARCHAR(50) COMMENT '微信用户ID',
            `create_time` DATETIME DATATIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间' 
        )";
        mysqli_query($wechat->conn, $sql);
    }
    /** 增加记录 */
    public static function add(?\Good_wechat $wechat)
    {
        $table = self::$table;
        $ver_code = rand(100000, 999999);
        $user_id = $wechat->from_user_name;
        $sql = "INSERT INTO `$table` (`ver_code`, `user_id`) VALUES ('$ver_code', '$user_id')";
        mysqli_query($wechat->conn, $sql);
        self::$ver_code = $ver_code;
    }
}
