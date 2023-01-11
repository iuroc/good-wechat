<?php

require('../good_wechat.php');
/**
 * 获取关键词和回复列表
 */
class Get_keyword_list
{
    public Good_wechat $wechat;
    public function __construct()
    {
        $this->wechat = new Good_wechat('../config.json');
    }
    public function get_list()
    {
    }
}
new Get_keyword_list();
