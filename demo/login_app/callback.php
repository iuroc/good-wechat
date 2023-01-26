<?php

/**
 * 回调接口
 */
class Callback
{
    /** Token 参数 */
    public string $token = '';
    /** 用户 ID */
    public string $user_id = '';
    public function __construct()
    {
        $this->token = $_GET['token'] ?? '';
        if (!$this->token) {
            echo 'token 值不能为空';
            die();
        }
        $this->init_db();
        $this->check_token();
    }
    /** 初始化第三方 Token 表 */
    public function init_db()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `test_user_token` (
            `user_id` VARCHAR(50),
            `token` VARCHAR()
        )";
    }
    /** 远程校验 Token */
    public function check_token()
    {
        // 机器人端的校验URL 以 http:// 开头
        $check_url = 'http://localhost/public-login/check_token.php';
        $result = file_get_contents($check_url . '?token=' . $this->token);
        $data = json_decode($result, true);
        $this->user_id = $data['user_id'] ?? '';
        if (!$this->user_id) {
            echo 'token 校验失败';
            die();
        }
        $this->add_token();
        setcookie('user-token', $this->token);
        setcookie('user-id', $this->user_id);
        header('location: ./');
    }
    /** 增加第三方 Token 记录 */
    public function add_token()
    {
    }
}

new Callback();
