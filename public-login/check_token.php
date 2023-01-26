<?php

namespace Login;

require('../good_wechat.php');
/**
 * 校验 Token
 */
class Check_token
{
    /** Token 参数 */
    public string $token;
    /** 机器人实例 */
    public \Good_wechat $wechat;
    /** user_token 表名 */
    public string $table_user_token;

    public function __construct()
    {
        $this->wechat = new \Good_wechat('../config.json');
        $this->table_user_token = $this->wechat->mysql_config['table']['user_token'];
        $this->token = $_GET['token'] ?? '';
        if (!$this->token) {
            echo 'token 值不能为空';
            die();
        }
        $this->check_token();
    }
    /** 校验 Token */
    public function check_token()
    {
        $this->delete_old_token();
        $time = time();
        $sql = "SELECT * FROM `{$this->table_user_token}` WHERE `token` = '{$this->token}' AND `expiry_date` > $time";
        $result = mysqli_query($this->wechat->conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            echo json_encode([
                'user_id' => null
            ]);
            die();
        }
        $data = mysqli_fetch_assoc($result);
        $user_id = $data['user_id'];
        echo json_encode([
            'user_id' => $user_id
        ]);
        $this->delete_now_token();
        die();
    }
    /** 删除过期 Token 记录 */
    public function delete_old_token()
    {
        $time = time();
        $sql = "DELETE FROM `{$this->table_user_token}` WHERE `expiry_date` < $time";
        mysqli_query($this->wechat->conn, $sql);
    }
    /** 删除当前 Token 记录 */
    public function delete_now_token()
    {
        $sql = "DELETE FROM `{$this->table_user_token}` WHERE `token` = '{$this->token}'";
        mysqli_query($this->wechat->conn, $sql);
    }
}
new Check_token();
