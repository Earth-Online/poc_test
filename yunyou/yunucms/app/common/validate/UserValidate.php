<?php
namespace app\common\validate;
use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        ['username', 'unique:admin', '管理员已经存在']
    ];
}