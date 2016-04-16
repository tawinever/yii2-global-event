<?php

use app\components\widgets\deptext\DepTextArea;
use app\models\User;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\Notify */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notify-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'event_code')->dropDownList(
	    array_flip(Yii::$app->notiHandler->getAllEvents()),
	    ['prompt' => "Choose event", 'id' => 'event-id']
    ); ?>

    <?= $form->field($model, 'sender_id')->widget(Select2::classname(), [
	    'data' => User::getUserList(),
	    'options' => ['placeholder' => 'Select a sender ...'],
	    'pluginOptions' => [
		    'allowClear' => true
	    ],
    ]); ?>

    <?= $form->field($model, 'recipient_id')->widget(DepDrop::classname(), [
	    'data' =>ArrayHelper::map(Yii::$app->notiHandler->getRecipientTypes($model->event_code),'id','name'),
	    'pluginOptions'=>[
		    'depends'=>['event-id'],
		    'placeholder'=>'Enter type of recipient',
		    'url'=>Url::to(['/notify/getrecipients'])
	    ]
    ]); ?>

	<?= $form->field($model, 'notifications')->widget(Select2::classname(), [
		'data' => Yii::$app->notiHandler->getNotiTypes(),
		'options' => ['placeholder' => 'Send to everyone', 'multiple' => true],
		'pluginOptions' => [
			'allowClear' => true,
		],
	]); ?>

	<?= $form->field($model, 'title')->widget(DepTextArea::classname(), [
		'tokens' => Yii::$app->notiHandler->getAttachTokens($model->event_code),
		'options' => ['rows' =>2],
		'depTargetId' => 'event-id',
		'ajaxUrl' => '/notify/getmodeltokens'
	]); ?>

	<?= $form->field($model, 'fulltext')->widget(DepTextArea::classname(), [
		'tokens' => Yii::$app->notiHandler->getAttachTokens($model->event_code),
		'options' => ['rows' =>5],
		'depTargetId' => 'event-id',
		'ajaxUrl' => '/notify/getmodeltokens'
	]); ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
