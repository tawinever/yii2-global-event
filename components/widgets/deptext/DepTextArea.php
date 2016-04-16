<?php

namespace app\components\widgets\deptext;

use kartik\base\InputWidget;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: Ertlek
 * Date: 14.04.2016
 * Time: 10:20
 * Modified mmedojevicbg text area tokens
 */
class DepTextArea extends InputWidget
{
    protected $asset;
    protected $fieldName;
    protected $textAreaId;
    protected $tokenContainerId;
    public $depTargetId;
    public $tokens = [];
    public $ajaxUrl;

    public function init()
    {
        parent::init();
            $this->fieldName = $this->attribute;
        $this->createTextAreaId();
        $this->createTokenContainerId();
    }

    public function run()
    {
        parent::run();
        $this->options['class'] = 'form-control';
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        $this->renderTokens();
        $this->registerClientScript();
    }

    private function renderTokens()
    {
        echo Html::beginTag('div', ['class' => 'available-tokens',
            'id' => $this->tokenContainerId]);
        echo Html::beginTag('div');
        echo 'Available tokens:';
        echo Html::endTag('div');
        $index = 0;
        foreach ($this->tokens as $group) {
            echo Html::beginTag('div');
                echo Html::beginTag('div', ['class' => 'token-header']);
                    echo $group[0];
                echo Html::endTag('div');
            foreach($group[1] as $token)
            {
                echo Html::beginTag('span', ['class' => 'token']);
                if($index === 0)
                    echo 'R.' . $token;
                else
                    echo "D$index." . $token;
                echo Html::endTag('span');
            }
            $index++;
            echo Html::endTag('div');
        }
        echo Html::endTag('div');
    }

    protected function registerClientScript()
    {
        $view = $this->getView();
        DepTextAreaAsset::register($view);
        $js = <<<EOT
        $('#$this->tokenContainerId').delegate('.token', 'click', function(){
            var token = $(this).html();
            insertAtCaret('$this->textAreaId', token);
        });
        $('.form-group').delegate('#$this->depTargetId', 'change', function(){
            updateTokenContainer('$this->ajaxUrl','$this->tokenContainerId','$this->depTargetId');
        });
EOT;
        $view->registerJs($js);
    }

    private function createTextAreaId()
    {
        return $this->textAreaId = $this->options['id'];
    }

    private function createTokenContainerId()
    {
        return $this->tokenContainerId = 'available-tokens-' . $this->fieldName;
    }
}
