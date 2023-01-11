<?php
require('../good_wechat.php');
/**
 * 删除记录
 */
class Delete
{
    public Good_wechat $wechat;
    /** 记录 ID */
    public string $id;
    public function __construct()
    {
        $this->wechat = new Good_wechat('../config.json');
    }
    /** 获取请求参数 */
    public function get_param()
    {
        $this->id = $_POST['id'] ?? '';
    }
    /** 执行删除 */
    public function run_delete()
    {
        $table_keyword = $this->wechat->mysql_config['table']['keyword'];
        $sql = "DELETE FROM `$table_keyword` WHERE `id` = {$this->id}";
        if (mysqli_query($this->wechat->conn, $sql)) {
            echo '删除成功';
        }
    }
}
$delete = new Delete();
$delete->get_param();
$delete->run_delete();
