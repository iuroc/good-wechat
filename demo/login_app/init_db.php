<?php

/**
 * 初始化数据库
 */
class Init_db
{
    /** 数据库连接 */
    public static mysqli $conn;
    /** 配置信息 */
    public static array $config;
    /** 数据库配置信息 */
    public static array $mysql_config;
    /** 数据表名称 */
    public static string $table_name;
    /** 初始化静态属性 */
    public static function init()
    {
        self::load_config('config.json');
        self::$table_name = self::$mysql_config['table']['user_token'];
        self::$conn = mysqli_connect(
            self::$mysql_config['host'],
            self::$mysql_config['username'],
            self::$mysql_config['password'],
            self::$mysql_config['database']
        );
        self::init_table();
    }
    /** 载入配置信息 */
    public static function load_config(string ...$config_path)
    {
        $path = $config_path[0] ?? 'config.json';
        self::$config = json_decode(file_get_contents($path), true);
        self::$mysql_config = self::$config['mysql'];
    }
    /** 初始化数据表 */
    public static function init_table()
    {
        $table = self::$table_name;
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
            `user_id` VARCHAR(50) NOT NULL COMMENT '用户ID',
            `token` VARCHAR(255) NOT NULL COMMENT 'Token 值',
            `expiry_date` INT COMMENT '过期时间',
            PRIMARY KEY (`user_id`)
        )";
        mysqli_query(self::$conn, $sql);
    }
}
Init_db::init();
