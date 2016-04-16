<?php

namespace app\controllers;

use app\components\AccessRule;
use app\models\User;
use Yii;
use app\models\Notify;
use app\models\NotifySearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * NotifyController implements the CRUD actions for Notify model.
 */
class NotifyController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => ['class' => AccessRule::className()],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Notify models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotifySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Notify model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
	    $model = $this->findModel($id);
	    $model->notifications = Yii::$app->notiHandler->toStringArrayType($model->notifications);
	    $model->recipient_id = is_null($model->recipient_id) ? "Everyone" : $model->recipient_id;
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Notify model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Notify();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//        if ($model->load(Yii::$app->request->post()) && false) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Notify model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

	/**
     * Geting types of recipient according to which event is chosen
     * which is passed via $_POST['depdrop_parents']
     * @return format is [['id' => 'blah','name  => 'blaha']]
     */
    public function actionGetrecipients()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents[0] != null && $parents[0] != '') {
                $out = Yii::$app->notiHandler->getRecipientTypes($parents[0]);
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>[] , 'selected'=>'']);
    }

    /**
     * Deletes an existing Notify model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Notify model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notify the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Notify::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetmodeltokens()
    {
        $event_value = Yii::$app->request->post('event');
        $tokens = Yii::$app->notiHandler->getAttachTokens($event_value);
        Yii::$app->response->format = Response::FORMAT_JSON;
        echo Json::encode($tokens);
        return;
    }
}
