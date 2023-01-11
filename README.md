# 微信公众号机器人系统

> 支持关键词回复、智能聊天、操作命令等功能

## 项目信息

- 作者：欧阳鹏
- 开发日期：2023 年 1 月 11 日
- 官网：https://apee.top

## 微信官方文档参考

- [微信公众平台开发概述](https://developers.weixin.qq.com/doc/offiaccount/Getting_Started/Overview.html)
- [接收普通消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_standard_messages.html)
- [接收事件推送](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_event_pushes.html)
- [被动回复用户消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Passive_user_reply_message.html)
- [模板消息接口](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html)
- [公众号一次性订阅消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/One-time_subscription_info.html)
- [模板消息运营规范](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Operation_Specifications.html)
- [消息加解密说明](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Message_encryption_and_decryption_instructions.html)
- [群发接口和原创校验](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html)

## 开始

- 修改 `config.json` 中的配置信息
- 导入 `good_wechat.php`
- 程序开始
  ```php
  <?php

  require('./good_wechat.php');

  $wechat = new Good_wechat();
  $wechat->start();
  $wechat->send_text('机器人测试开始啦');
  ```

## API 文档

### 方法

- send_text(string $text)
  - 发送文本消息