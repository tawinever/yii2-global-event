<?php
/**
 * Created by PhpStorm.
 * User: Ertlek
 * Date: 15.04.2016
 * Time: 18:23
 */
use yii\helpers\Html;

$class = $model->status === \app\models\Message::STATUS_NEW ? 'alert-success' : 'alert-warning';
?>

<div class = "alert <?=$class?>">


    <h4>
        <? if($class === "alert-success")
        echo Html::a('<i class = "glyphicon glyphicon-ok"> </i>', ['site/setread'], [
        'data'=>[
            'method' => 'post',
            'params'=>['id'=>$model->id],
        ]
        ])?>
        <?= $model->title ?>
    </h4>
    <blockquote>
        <p><?= $model->fulltext ?></p>
        <footer><?= $model->from0->username?> <i><?= $model->from0->email?></i><cite> <?= $model->created_at ?></cite></footer>
    </blockquote>
</div>
