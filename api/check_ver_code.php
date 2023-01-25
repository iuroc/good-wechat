<?php
require('../good_wechat.php');
/**
 * 校验验证码
 * @author 欧阳鹏
 */
class Check_ver_code
{
    public Good_wechat $wechat;
    public function __construct()
    {
        $this->wechat = new Good_wechat('../config.json');
    }
}
