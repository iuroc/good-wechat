<?php

namespace Rule;

/** 自定义规则 */
interface IRule
{
    /**
     * 开启规则
     * @param array $args 命令参数，如 `['git', 'init']`
     * @param \Good_wechat $wechat 机器人实例
     * @return string 需要回复给用户的内容
     */
    public static function run(?array $args = [], ?\Good_wechat $wechat): string;
}
