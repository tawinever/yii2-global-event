<?php
namespace app\components\widgets\deptext;
use yii\web\AssetBundle;

class DepTextAreaAsset extends AssetBundle
{
	public $js = [
		'script.js',
		'update.js',
	];
	public $css = [
		'style.css',
	];
	public $depends = [
		'yii\web\YiiAsset',
		'yii\web\JqueryAsset',
	];
	public function init()
	{
		$this->sourcePath = __DIR__ . '/assets';
		parent::init();
	}
}