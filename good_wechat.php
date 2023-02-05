<?php

/**
 * Good-Wechat 微信公众号机器人
 * @author 欧阳鹏
 * @version 1.1.1
 */
class Good_wechat
{
    /** 解析后的输入数据 */
    public array $input_data;
    /** 收件人，即机器人 */
    public string $to_user_name;
    /** 发件人，即微信用户ID */
    public string $from_user_name;
    /** 接收到的消息类型 */
    public string $msg_type;
    /** 事件类型 */
    public string $event_type;
    /** 接收到的消息内容 */
    public string $content;
    /** 配置信息 */
    public array $config;
    /** MySQL 配置信息 */
    public array $mysql_config;
    /** 微信公众号配置的 token */
    public string $token;
    /** 数据库连接 */
    public mysqli $conn;
    /**
     * @param string $config_path 配置文件路径，默认为当前文件夹下 config.json
     */
    public function __construct(...$config_path)
    {
        $this->load_config($config_path);
        $this->init_db();
    }
    /** 载入配置信息 */
    public function load_config(array $config_path)
    {
        $path = $config_path[0] ?? 'config.json';
        $this->config = json_decode(file_get_contents($path), true);
        $this->mysql_config = $this->config['mysql'];
    }
    /** 初始化数据库 */
    public function init_db()
    {
        $this->conn = mysqli_connect(
            $this->mysql_config['host'],
            $this->mysql_config['username'],
            $this->mysql_config['password'],
            $this->mysql_config['database']
        );
        $table_keyword = $this->mysql_config['table']['keyword'];
        mysqli_query($this->conn, "CREATE TABLE IF NOT EXISTS `$table_keyword` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `keyword` VARCHAR(255) NOT NULL,
            `reply` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`)
        )");
    }
    /** 开启机器人 */
    public function start()
    {
        $this->load_input_data();
        $this->match_keyword();
    }

    /** 校验 Token
     * @link [接入指南__微信开发文档](https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Access_Overview.html)
     */
    public function check_signature()
    {
        $signature = $_GET['signature'] ?? '';
        $timestamp = $_GET['timestamp'] ?? '';
        $nonce = $_GET['nonce'] ?? '';
        $token = $this->token ?? '';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    /** 匹配关键词并回复 */
    public function match_keyword()
    {
        if ($this->msg_type != 'text') {
            return;
        }
        $table_keyword = $this->mysql_config['table']['keyword'];
        $keyword = mysqli_real_escape_string($this->conn, $this->content);
        $sql = "SELECT `reply` FROM `$table_keyword` WHERE `keyword` = '$keyword'";
        $result = mysqli_query($this->conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            $reply = mysqli_fetch_assoc($result)['reply'];
            $this->send_text($reply);
        }
    }
    /** 设置 token 值，如果不设置，则不校验 */
    public function set_token(string $token)
    {
        $this->token = $token;
    }
    /** 获取并解析输入数据 */
    public function load_input_data()
    {
        $this->token = $this->token ?? '';
        $input_text = file_get_contents('php://input');
        if ($this->token && $this->check_signature()) {
            if ($_GET['echostr'] ?? '') {
                echo $_GET['echostr'] ?? '';
                die();
            }
        } elseif ($this->token) {
            echo '校验失败';
            die();
        }
        if (!$input_text) {
            echo '输入为空';
            die();
        }

        $this->input_data = (array)simplexml_load_string($input_text, null, LIBXML_NOCDATA);
        $this->to_user_name = $this->input_data['ToUserName'];
        $this->from_user_name = $this->input_data['FromUserName'];
        $this->msg_type = $this->input_data['MsgType'];
        $this->content = $this->input_data['Content'] ?? '';
        $this->event_type = $this->input_data['Event'] ?? '';
        if ($this->msg_type == 'text' && !$this->content) {
            $this->send_text('亲，请输入内容哦');
        }
    }
    /** 发送文本数据，注意本方法只能调用一次
     * @param string $text 待发送内容
     */
    public function send_text(string $text)
    {
        $out_text = $this->parse_out('
        <xml>
            <ToUserName><![CDATA[' . $this->from_user_name . ']]></ToUserName>
            <FromUserName><![CDATA[' . $this->to_user_name . ']]></FromUserName>
            <CreateTime>' . time() . '</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[' . $this->parse_out($text) . ']]></Content>
        </xml>');
        echo $out_text;
        die();
    }
    /** 处理输出文本，去除首尾空白，去除每行开头空白
     * @param string $out_text 待发送内容
     */
    public function parse_out($out_text)
    {
        $new_text = preg_replace('/^[ \t]+/m', '', $out_text);
        $new_text = trim($new_text);
        return $new_text;
    }
    /**
     * 增加规则
     * @param string $pattern 正则规则
     * @param callable $callback 回调函数，返回需要发送的内容
     */
    public function add_rule(string $pattern, callable $callback)
    {
        if ($this->msg_type != 'text') {
            return;
        }
        $args = preg_split('/\s+/', $this->content);
        if (preg_match($pattern, $args[0] ?? '')) {
            $callback_text = call_user_func($callback, $this, $args);
            if ($callback_text) {
                $this->send_text($callback_text);
            }
        }
    }
    /** 增加事件规则 */
    public function add_event_rule(string $event_type, callable $callback)
    {
        if ($this->event_type == $event_type) {
            $callback_text = call_user_func($callback, $this);
            if ($callback_text) {
                $this->send_text($callback_text);
            }
        }
    }
}
date_default_timezone_set('Asia/Shanghai');
