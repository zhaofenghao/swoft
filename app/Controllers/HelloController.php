<?php
/**
 * Created by PhpStorm.
 * User: zfh
 * Date: 2018-11-07
 * Time: 11:09
 */
namespace App\Controllers;

use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;

/**
 * Class HelloController
 * @package App\Controllers
 * @Controller()
 */
class HelloController
{
    /**
     * @RequestMapping()
     * @return string
     */
    public function test()
    {
        return "Hello World";
    }

    public function testFunc()
    {
//        $result = cache()->set('nameFunc', '235 redis ready');
        $name   = cache()->hgetall('15950568797');
        return $name;
//        return [$result, $name];
    }
}