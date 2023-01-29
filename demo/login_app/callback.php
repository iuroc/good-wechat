<?php
require_once('./init_db.php');
/**
 * 回调接口
 */
class Callback
{
    /** Token 参数 */
    public string $token = '';
    /** 用户 ID */
    public string $user_id = '';
    /** 数据表名 */
    public string $table_name;
    /** 过期时间戳 */
    public int $expiry;
    public function __construct()
    {
        $this->token = $_GET['token'] ?? '';
        if (!$this->token) {
            echo 'token 值不能为空';
            die();
        }
        $this->table_name = Init_db::$table_name;
        $this->check_token();
        $this->delete_old_token();
    }
    /** 删除过期 Token */
    public function delete_old_token()
    {
        $time = time();
        $sql = "DELETE FROM `{$this->table_name}` WHERE `expiry_date` < $time";
        mysqli_query(Init_db::$conn, $sql);
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
            echo 'token 联网校验失败';
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
        $this->expiry = time() + 30 * 24 * 60 * 60;
        if ($this->user_id_exists()) {
            $sql = "UPDATE `{$this->table_name}` SET `token` = '{$this->token}', `expiry_date` = '{$this->expiry}' WHERE `user_id` = '{$this->user_id}'";
        } else {
            $sql = "INSERT INTO `{$this->table_name}` VALUES ('{$this->user_id}', '{$this->token}', '{$this->expiry}')";
        }
        mysqli_query(Init_db::$conn, $sql);
    }
    /** 判断用户记录是否存在 */
    public function user_id_exists()
    {
        $sql = "SELECT NULL FROM `{$this->table_name}` WHERE `user_id` = '{$this->user_id}'";
        $result = mysqli_query(Init_db::$conn, $sql);
        return mysqli_num_rows($result) > 0;
    }
}

new Callback();
