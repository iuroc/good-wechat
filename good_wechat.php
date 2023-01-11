<?php

/** Good-Wechat 微信公众号机器人
 * @author 欧阳鹏
 */
class Good_wechat
{
    /** 解析后的输入数据 */
    public array $input_data;
    /** 收件人 */
    public string $to_user_name;
    /** 发件人 */
    public string $from_user_name;
    /** 消息类型 */
    public string $msg_type;
    /** 配置信息 */
    public array $config;
    /** MySQL 配置信息 */
    public array $mysql_config;
    /** 数据库连接 */
    public mysqli $conn;
    public function __construct()
    {
        $this->load_config();
    }
    /** 载入配置信息 */
    public function load_config()
    {
        $this->config = json_decode(file_get_contents('config.json'), true);
        $this->mysql_config = $this->config['mysql'];
        $this->conn = mysqli_connect(
            $this->mysql_config['host'],
            $this->mysql_config['username'],
            $this->mysql_config['password'],
            $this->mysql_config['database']
        );
    }
    /** 开启机器人 */
    public function start()
    {
        $this->load_input_data();
    }
    /** 获取并解析输入数据 */
    private function load_input_data()
    {
        $input_text = file_get_contents('php://input');
        if (!$input_text) {
            die('输入为空');
        }
        $this->input_data = (array)simplexml_load_string($input_text, null, LIBXML_NOCDATA);
        $this->to_user_name = $this->input_data['ToUserName'];
        $this->from_user_name = $this->input_data['FromUserName'];
        $this->msg_type = $this->input_data['MsgType'];
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
    }
    /** 处理输出文本，去除首尾空白，去除每行开头空白
     * @param string $out_text 待发送内容
     */
    private function parse_out($out_text)
    {
        $new_text = preg_replace('/^\s+/m', '', $out_text);
        $new_text = trim($new_text);
        return $new_text;
    }
}
