# Good-Wechat

> 这是一个支持 **关键词回复 ​**和 **自定义规则 ​**的微信公众号机器人程序，可以轻松完成接入

## 关于项目

* 作者：欧阳鹏
* 开发日期：2023 年 1 月 11 日
* 官网：[https://apee.top](https://apee.top)

## 接入方法

> 要求 PHP 版本 8.1 及以上

* 克隆仓库，或者下载 zip 压缩包文件
* 将代码上传到服务器
* 在 `config.json` 中修改数据库配置信息
* 将 `wechat.php` 的公网访问 URL 填写到微信公众号后台配置中
* 在微信中向公众号发送任意消息，即可收到回复内容

## 机器人管理

* 访问 `/admin/index.html`，可打开机器人控制台
* 访问 `/request/index.html`，可在线调试机器人

## 自定义开发

1. 导入 `Wechat_good` 类并实例化

    ```php
    require('./good_wechat.php');
    $wechat = new Good_wechat();
    ```
2. 设置 token

    ```php
    $wechat->set_token('xxxxxxx');
    ```
3. 启动机器人

    ```php
    $wechat->start();
    ```
4. 增加自定义规则

    * [add_rule 方法说明](#add_rule)

    ```php
    $wechat->add_rule('/.*?music.*?/', function (\Good_wechat $wechat, $args) {
        return '你好，我是回复内容';
    });
    ```
5. 默认回复内容

    ```php
    $wechat->send_text('没有匹配到结果哦');
    ```

## API 文档

### 方法

#### set_token

* 用于设置 `token`，`token` 值和微信公众平台的配置保持一致

#### start

* 用于启动机器人，启动后，开始读取用户消息，并匹配关键词参数
* 只有在创建机器人时，需要调用调用该方法

#### add_rule

* 说明

  * 增加自定义匹配规则，则自定义程序逻辑
* 语法

  ```php
  add_rule(string $pattern, callable $callback)
  ```
* 参数

  * `pattern`：正则表达式，用于匹配指令类型（用户消息使用空格分割，取第一项为指令类型）
  * `callback`：回调函数，包含机器人实例和数组参数 `$args`，代表指令参数列表

    ```php
    function (\Good_wechat $wechat, array $args) {}
    ```

#### send_text

* 说明

  * 向用户回复内容
  * 该方法只会调用一次，调用完成后程序终止，即用户会收到第一次调用该方法时发送的内容

#### send_news

* 说明

  * 向用户回复消息卡片
* 语法

  ```php
  send_news(string $title, string $sub_title, string $url)
  ```
* 参数

  * `title`：卡片标题
  * `sub_title`：卡片子标题
  * `url`：卡片链接地址

## 微信官方文档参考

* [微信公众平台开发概述](https://developers.weixin.qq.com/doc/offiaccount/Getting_Started/Overview.html)
* [接收普通消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_standard_messages.html)
* [接收事件推送](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_event_pushes.html)
* [被动回复用户消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Passive_user_reply_message.html)
* [模板消息接口](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html)
* [公众号一次性订阅消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/One-time_subscription_info.html)
* [模板消息运营规范](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Operation_Specifications.html)
* [消息加解密说明](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Message_encryption_and_decryption_instructions.html)
* [群发接口和原创校验](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Batch_Sends_and_Originality_Checks.html)