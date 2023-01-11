<?php

/**
 * 机器人主程序
 */

require('./good_wechat.php');

$wechat = new Good_wechat();
$wechat->start();
// $wechat->send_text('你刚刚发送的“' . $wechat->content . '”暂时没有答案哦');
$wechat->send_text('系统升级中，敬请谅解

系统已设置部分保留关键词，你可以试试这些关键词

更多资讯可以关注：<a href="https://apee.top">鹏优创官方网站</a>');
