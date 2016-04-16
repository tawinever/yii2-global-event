<?php
/**
 * Created by PhpStorm.
 * User: Ertlek
 * Date: 15.04.2016
 * Time: 17:56
 */
use yii\widgets\ListView;

echo ListView::widget( [
    'dataProvider' => $dataProvider,
    'itemView' => '_item',
] );