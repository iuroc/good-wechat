<?php

namespace Login;

require('../good_wechat.php');
/**
 * 初始化 `gw_login_app` 表
 */
class Init_db
{
    /** 机器人实例 */
    public \Good_wechat $wechat;
    public function __construct()
    {
        $this->wechat = new \Good_wechat('../config.json');
        $this->create();
    }
    /** 创建数据表 */
    public function create()
    {
        $table = $this->wechat->mysql_config['table']['login_app'];
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
            `app_id` INT PRIMARY KEY COMMENT '应用ID',
            `callback_url` VARCHAR(255) COMMENT '回调接口URL',
            `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '应用创建时间'
        )";
        mysqli_query($this->wechat->conn, $sql);
    }
}
new Init_db();
