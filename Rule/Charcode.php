<?php

namespace Rule;

require('./IRule.php');
/**
 * 验证码发送
 */
class Charcode implements IRule
{
    public static function run(array $args = []): string
    {
        return '666';
    }
}
