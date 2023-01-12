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

## 部署方法

> 注意：需要 PHP 版本 8.1 以上

- 克隆仓库
- 修改 `config.json` 中的配置信息
- 访问 `/request/index.html` 可以调试机器人
- 访问 `/admin/index.html` 打开机器人控制面板
- 将 `wechat.php` 的公网访问地址配置到微信公众号后台即可接入

## API 文档

### 方法

- send_text(string $text)
  - 发送文本消息
- start()
  - 开启机器人，在实例化 `Good_wechat` 类后调用
- add_rule(string $pattern, callable $callback)
  - 说明：增加自定义规则
  - 参数
    - $pattern 正则表达式，用于匹配用户发送的指令类型（用空格分割后的第一项）
    - $callback 回调函数，如果需要发送消息，需要使用 `return` 直接返回消息，add_rule 方法将自动发送消息
  - 示例
    ```php
    $wechat->add_rule('/.*?music.*?/', function ($args) {
        return '你输入的内容包含了 music 哦';
    });
    ```