<?php

use function PHPSTORM_META\type;

/**
 * 回调接口
 */
class Callback
{
    /** Token 参数 */
    public string $token = '';
    /** 用户 ID */
    public string $user_id = '';
    /** 配置信息 */
    public array $config;
    /** MySQL 数据库配置信息 */
    public array $mysql_config;
    /** 数据库连接 */
    public mysqli $conn;
    public function __construct()
    {
        $this->token = $_GET['token'] ?? '';
        if (!$this->token) {
            echo 'token 值不能为空';
            die();
        }
        $this->load_config(['../../config.json']);
        $this->init_db();
        $this->check_token();
    }
    /** 载入配置信息 */
    public function load_config(array $config_path)
    {
        $path = $config_path[0] ?? 'config.json';
        $this->config = json_decode(file_get_contents($path), true);
        $this->mysql_config = $this->config['mysql'];
    }
    /** 初始化第三方 Token 表 */
    public function init_db()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `test_user_token` (
            `user_id` VARCHAR(50) NOT NULL COMMENT '用户ID',
            `token` VARCHAR(255) NOT NULL COMMENT 'Token 值',
            `expiry_date` INT COMMENT '过期时间',
            PRIMARY KEY (`user_id`)
        )";
        $this->conn = mysqli_connect(
            $this->mysql_config['host'],
            $this->mysql_config['username'],
            $this->mysql_config['password'],
            $this->mysql_config['database']
        );
        mysqli_query($this->conn, $sql);
    }
    /** 远程校验 Token */
    public function check_token()
    {
        // 机器人端的校验URL 以 http:// 开头
        $check_url = 'http://api.apee.top/wechat/public/ponconsoft/good-wechat/public-login/check_token.php';
        $result = file_get_contents($check_url . '?token=' . $this->token);
        $data = json_decode($result, true);
        $this->user_id = $data['user_id'] ?? '';
        if (!$this->user_id) {
            echo 'token 校验失败';
            die();
        }
        $this->add_token();
        setcookie('user-token', $this->token, time() + 30 * 24 * 60 * 60, '/');
        setcookie('user-id', $this->user_id, time() + 30 * 24 * 60 * 60, '/');
        header('location: ./');
    }
    /** 增加第三方 Token 记录 */
    public function add_token()
    {
        // $sql = "INSERT INTO `test_user_token`"
    }
}

new Callback();
