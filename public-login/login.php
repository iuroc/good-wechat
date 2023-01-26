<?php

namespace Login;

require('../good_wechat.php');
/**
 * 统一扫码登录系统
 * @author 欧阳鹏
 * @version 1.0
 * @link https:/apee.top
 */
class Login
{
    /** 机器人实例 */
    public \Good_wechat $wechat;
    /** 应用 ID */
    public string $app_id;
    /** 验证码 */
    public string $ver_code;
    /** 回调接口 URL */
    public string $callback_url;
    /** 错误提示 */
    public string $error_msg = '';
    /** user_token 表名 */
    public string $table_user_token;
    /** 用户 ID */
    public string $user_id;
    /** Token 过期时间（秒） 机器人端 Token 保留时间 第三方需要在有效期内完成校验 */
    public int $expiry = 5 * 60;
    public function __construct()
    {
        $this->wechat = new \Good_wechat('../config.json');
        $this->table_user_token = $this->wechat->mysql_config['table']['user_token'];
        $this->init_db();
        $this->check_app_id();
        $this->check_ver_code();
    }
    /** 校验应用 ID */
    public function check_app_id()
    {
        $this->app_id = $_GET['app_id'] ?? '';
        if (!is_numeric($this->app_id)) {
            echo 'app_id 错误';
            die();
        }
        $table = $this->wechat->mysql_config['table']['login_app'];
        $sql = "SELECT * FROM `$table` WHERE `app_id` = '{$this->app_id}'";
        $result = mysqli_query($this->wechat->conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            echo 'app_id 不存在';
            die();
        }
        $data = mysqli_fetch_assoc($result);
        $this->callback_url = $data['callback_url'];
    }
    /** 校验验证码 */
    public function check_ver_code()
    {
        $this->ver_code = $_POST['ver_code'] ?? '';
        if (!$this->ver_code) {
            return;
        }
        $table = $this->wechat->mysql_config['table']['ver_code'];
        $time = time();
        $sql = "SELECT * FROM `$table` WHERE `ver_code` = '{$this->ver_code}' AND `expiry_date` > $time";
        $result = mysqli_query($this->wechat->conn, $sql);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (!$data) {
            $this->error_msg = '验证码错误';
            return;
        }
        $this->user_id = $data['user_id'];
        $this->delete_old_ver_code();
        $this->add_token();
    }
    /** 创建 token 记录*/
    public function add_token()
    {
        $random_bytes = random_bytes(64);
        $token = bin2hex($random_bytes);
        if ($this->if_user_id_exists()) {
            $this->update_token($token);
            return;
        }
        $expiry_date = time() + $this->expiry;
        $sql = "INSERT INTO `{$this->table_user_token}` (`user_id`, `token`, `expiry_date`, `app_id`)
                VALUES ('{$this->user_id}', '$token', $expiry_date, '{$this->app_id}')";
        mysqli_query($this->wechat->conn, $sql);
        $this->callback_token($token);
    }
    /**
     * 更新 Token 记录
     * @param string $token 生成的 Token 值
     */
    public function update_token(string $token)
    {
        $expiry_date = time() + $this->expiry;
        $sql = "UPDATE `{$this->table_user_token}` SET `token` = '$token', `expiry_date` = $expiry_date WHERE `user_id` = '{$this->user_id}' AND `app_id` = '{$this->app_id}'";
        mysqli_query($this->wechat->conn, $sql);
        $this->callback_token($token);
    }
    /** 判断用户 Token 记录是否存在 */
    public function if_user_id_exists(): bool
    {
        $sql = "SELECT * FROM `{$this->table_user_token}` WHERE `user_id` = '{$this->user_id}' AND `app_id` = '{$this->app_id}'";
        $result = mysqli_query($this->wechat->conn, $sql);
        return mysqli_num_rows($result) > 0;
    }
    /** 初始化数据表 */
    public function init_db()
    {
        $table = $this->wechat->mysql_config['table']['login_app'];
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
            `app_id` VARCHAR(255) PRIMARY KEY COMMENT '应用ID',
            `callback_url` VARCHAR(255) COMMENT '回调接口URL',
            `create_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '应用创建时间'
        )";
        mysqli_query($this->wechat->conn, $sql);

        $table = $this->wechat->mysql_config['table']['user_token'];
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
            `user_id` varchar(255) NOT NULL COMMENT '用户ID',
            `token` varchar(255) NOT NULL COMMENT '令牌',
            `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
            `expiry_date` int NOT NULL COMMENT '过期时间',
            `app_id` varchar(255) NOT NULL COMMENT '应用ID',
            PRIMARY KEY (`user_id`, `app_id`) USING BTREE,
            INDEX `app_id`(`app_id` ASC) USING BTREE,
            CONSTRAINT `gw_user_token_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `gw_login_app` (`app_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
        )";
        mysqli_query($this->wechat->conn, $sql);
    }
    /**
     * 向回调接口返回 token
     * @param string $token Token 值
     */
    public function callback_token(string $token)
    {
        if (!filter_var($this->callback_url, FILTER_VALIDATE_URL)) {
            echo '回调接口异常';
            die();
        }
        $url = $this->callback_url . '?token=' . $token;
        header('location:' . $url);
    }
    /** 删除过期验证码 */
    public function delete_old_ver_code()
    {
        $table = $this->wechat->mysql_config['table']['ver_code'];
        $time = time();
        $sql = "DELETE FROM `$table` WHERE `ver_code` = '{$this->ver_code}' OR `expiry_date` < $time";
        mysqli_query($this->wechat->conn, $sql);
    }
}
$login = new Login();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>统一扫码登录系统</title>
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: '仓耳渔阳体';
            padding: 5%;
        }

        .main {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .input-group {
            display: flex;
            width: 260px;
            max-width: 100%;
        }

        .input-group * {
            font-size: 20px;
        }

        .input-group .ver_code {
            border-radius: 0;
            padding: 0 10px;
            width: 100%;
            outline: none;
            border-style: solid;
            border-color: brown;
            border-width: 3px 0 3px 3px;
            border-radius: 5px 0 0 5px;
        }

        .input-group .login {
            border: 0;
            background-color: brown;
            color: white;
            padding: 8px 15px;
            white-space: nowrap;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            user-select: none;
            -moz-user-select: none;
            -webkit-user-select: none;
        }

        .error_msg {
            margin-top: 20px;
            color: red;
            font-size: 20px;
        }
    </style>
</head>

<body>
    <div class="main">
        <h2 style="margin-bottom: 10px; font-size: 30px;">微信扫码登录</h2>
        <img src="./img/qrcode.jpg" alt="二维码" style="width: 300px; max-width: 100%;" draggable="false">
        <div style="font-size: 20px; margin-bottom: 20px;">&nbsp;&nbsp;请扫码 <b>发送</b> 关键词【<b style="color: red;">验证码</b>】</div>
        <form class="input-group" method="post" action="?app_id=<?php echo $login->app_id ?>">
            <input type="text" name="ver_code" class="ver_code" placeholder="请输入验证码" required autofocus>
            <input type="submit" class="login" value="登录">
        </form>
        <?php if ($login->error_msg) : ?>
            <div class="error_msg">
                <?php echo $login->error_msg ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>