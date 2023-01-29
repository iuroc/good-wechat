<?php
require_once('./init_db.php');
/** 第三方自建，校验 Token */
class Check_token
{
    /** 用户 ID */
    public static string $user_id;
    /** 令牌 */
    public static string $token;
    /** 校验 Token */
    public static function check()
    {
        $table = Init_db::$table_name;
        $user_id = $_COOKIE['user-id'] ?? '';
        $token = $_COOKIE['user-token'] ?? '';
        $sql = "SELECT NULL FROM `$table` WHERE `user_id` = '$user_id' AND `token` = '$token' AND `expiry_date` > " . time();
        $result = mysqli_query(Init_db::$conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            header('location: http://api.apee.top/wechat/public/ponconsoft/good-wechat/public-login/login.php?app_id=' . Init_db::$config['app_id']);
        }
    }
}
