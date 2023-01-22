<?php

/**
 * 机器人主程序
 */

require('./good_wechat.php');
require('./Rule/IRule.php');
require('./Rule/Ver_code.php');
$wechat = new Good_wechat();
$wechat->set_token('gyfweyatgfyredgdfgfd');
$wechat->start();
$wechat->add_rule('/.*?music.*?/', function () {
    return '你输入的内容包含了 music 哦';
});
$wechat->add_rule('/^\s*验证码\s*$/', function ($args) {
    return \Rule\Ver_code::run($args);
});
// 如果没有匹配结果，将直接回复下面的内容
$wechat->send_text('系统升级中，敬请谅解
系统已设置部分保留关键词，你可以试试这些关键词
更多资讯可以关注：<a href="https://apee.top">鹏优创官方网站</a>');
