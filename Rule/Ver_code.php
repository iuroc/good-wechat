<?php

namespace Rule;

/** 验证码生成并发送 */
class Ver_code implements IRule
{
    /** 数据表名 */
    public static string $table;
    /** 验证码内容 */
    public static string $ver_code;
    /** 机器人实例 */
    public static \Good_wechat $wechat;
    public static function run(\Good_wechat $wechat, array $args): string
    {
        self::$table = $wechat->mysql_config['table']['ver_code'];
        self::init_table();
        self::delete_old_ver_code();
        self::add();
        return self::$ver_code;
    }
    /** 删除旧的验证码 */
    public static function delete_old_ver_code()
    {
        $table = self::$table;
        $user_id = self::$wechat->from_user_name;
        $sql = "DELETE FROM `$table` WHERE `user_id` = '$user_id'";
        mysqli_query(self::$wechat->conn, $sql);
    }
    /** 初始化数据表，若数据表不存在则自动创建 */
    public static function init_table()
    {
        $table = self::$table;
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `ver_code` VARCHAR(20) COMMENT '验证码',
            `user_id` VARCHAR(50) COMMENT '微信用户ID',
            `create_time` DATETIME DATATIME DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间' 
        )";
        mysqli_query(self::$wechat->conn, $sql);
    }
    /** 增加记录 */
    public static function add()
    {
        $table = self::$table;
        $ver_code = rand(100000, 999999);
        $user_id = self::$wechat->from_user_name;
        $sql = "INSERT INTO `$table` (`ver_code`, `user_id`) VALUES ('$ver_code', '$user_id')";
        mysqli_query(self::$wechat->conn, $sql);
        self::$ver_code = $ver_code;
    }
}
