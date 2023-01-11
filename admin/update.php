<?php
require('../good_wechat.php');
/**
 * 增加 & 编辑关键词回复
 */
class Update
{
    public Good_wechat $wechat;
    /** 关键词 */
    public string $keyword;
    /** 回复内容 */
    public string $reply;
    public function __construct()
    {
    }
    /** 获取请求参数 */
    public function get_param()
    {
        $this->keyword = $_POST['keyword'] ?? '';
        $this->reply = $_POST['replay'] ?? '';
    }
    /** 判断记录是否存在 */
    public function if_exists(): bool
    {
        $table_keyword = $this->wechat->mysql_config['table']['keyword'];
        $keyword = mysqli_real_escape_string($this->wechat->conn, $this->keyword);
        $sql = "SELECT * FROM `$table_keyword` WHERE `keyword` = '$keyword'";
        $result = mysqli_query($this->wechat->conn, $sql);
        return mysqli_num_rows($result) > 0;
    }
    /** 更新记录 */
    public function update()
    {
        $table_keyword = $this->wechat->mysql_config['table']['keyword'];
        $keyword = mysqli_real_escape_string($this->wechat->conn, $this->keyword);
        $reply = mysqli_real_escape_string($this->wechat->conn, $this->reply);
        $sql = "UPDATE `$table_keyword` SET `replay` = '$reply' WHERE `keyword` = '$keyword'";
        mysqli_query($this->wechat->conn, $sql);
    }
    /** 增加记录 */
    public function add()
    {
        $table_keyword = $this->wechat->mysql_config['table']['keyword'];
        $keyword = mysqli_real_escape_string($this->wechat->conn, $this->keyword);
        $reply = mysqli_real_escape_string($this->wechat->conn, $this->reply);
        $sql = "INSERT INTO `$table_keyword` (`keyword`, `reply`) VALUES ('$keyword', '$reply')";
        mysqli_query($this->wechat->conn, $sql);
    }
}
$update = new Update();
$update->get_param();
if ($update->if_exists()) {
    $update->update();
} else {
    $update->add();
}
