<?php

/**
 * 机器人主程序
 */

require('./good_wechat.php');

$wechat = new Good_wechat();
$wechat->start();
$wechat->send_text('机器人测试开始啦');
