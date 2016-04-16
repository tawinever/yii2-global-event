<?php
/**
 * Created by PhpStorm.
 * User: Ertlek
 * Date: 15.04.2016
 * Time: 10:50
 */

namespace app\components;


use yii\base\Event;

class NotificationEvent extends Event
{
    public $target;
    public $attach = [];
}