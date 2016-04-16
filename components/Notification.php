<?php
/**
 * Created by PhpStorm.
 * User: Rauan
 * Date: 11.04.2016
 * Time: 14:57
 */

namespace app\components;

use app\models\Message;
use app\models\Notify;
use app\models\User;
use Yii;
use yii\base\Component;

class Notification extends Component
{
    const TYPE_BROWSER = 0;
    const TYPE_EMAIL = 1;

    const RECIPIENT_CERTAIN = "Certain recipient";
    const RECIPIENT_ALL = "Everyone";


    public $models;

    /**
     *Including all NEVENTs to handleevent func
     */
    public function init()
    {
        parent::init();
        foreach($this->getAllEvents() as $event_value)
        {
            $this->on($event_value, [$this, 'handleEvent']);
        }
    }

    /**
     * All events will be handled by this function.Thus each of events do
     * stuffs that need to do.
     */
    public function handleEvent($event)
    {
        $event_value = $event->name;
        $notifies = Notify::find()->where(['event_code' => $event_value])->all();
        foreach($notifies as $noti)
        {
            $recipients = $noti->recipient_id === self::RECIPIENT_CERTAIN ? [$event->target] : User::find()->all();
            foreach($recipients as $recipient)
            {
                if(get_class($recipient) === "app\models\User")
                {
                    $converted = $this->templateMethod($recipient,$event->attach,[$noti->title, $noti->fulltext]);
                    $msg = [
                        "title" => $converted[0],
                        "fulltext" => $converted[1],
                    ];
                    $this->sendNotification($noti->sender,$recipient,$msg,$noti->notifications);
                }
            }

        }
    }


    /**
     * In future we will add constants like TYPE_TELEGRAM or TYPE_SMS
     * and write here how to send actually
     * @param $target
     * @param $msg
     * @param $typeArray
     */
    private function sendNotification($sender,$target, $msg, $typeArray)
    {
        if(in_array(self::TYPE_BROWSER,$typeArray))
        {
            $model = new Message;
            $model->from = $sender->id;
            $model->to = $target->id;
            $model->title = $msg['title'];
            $model->fulltext = $msg['fulltext'];
            $model->save();
        }
        if(in_array(self::TYPE_EMAIL,$typeArray))
        {
            \Yii::$app->mail->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => $sender->username])
                ->setTo($target->email)
                ->setSubject($msg['title'])
                ->setTextBody($msg['fulltext'])
                ->send();
        }
    }
    /**
     * @param $recipient - to whom (User object)
     * @param $attach - other Objects
     * @param $text - template method handling text
     */
    private function templateMethod($recipient, $attach, $textArray)
    {
        $tokens = [];
        $tokens = $this->tokenize($tokens,$recipient,"R\.");
        foreach($attach as $perAttach)
        {
            $index = 0;
            $prefix ="D" . ++$index."\.";
            $tokens = $this->tokenize($tokens, $perAttach,$prefix);
        }
        $pattern = array_keys($tokens);
        $replacement = array_values($tokens);
        $newtextArray = [];
        foreach($textArray as $text)
        {
            $newtextArray[] = preg_replace($pattern,$replacement,$text);
        }
        return $newtextArray;
    }

    /**
     * !!! Here we are changing KEY in the way that
     * we able to use directly like regedx pattern
     * @param $aggregator - array that aggregates all tokens
     * @param $model - current attributes obtaining object
     * @param $prefix - user prefix before keys of attributes
     * @return mixed - aggregator added attributes of model
     */
    private function tokenize($aggregator, $model, $prefix)
    {
        $attrs = $model->getAttributes();
        foreach($attrs as $key => $value)
        {
            $aggregator["/\{\{".$prefix.$key."\}\}/"] = $value;
        }
        return $aggregator;
    }


    /**
     * Gives all defined NEvent constants of the argument class
     * @param $className
     * $consType = type of constants. (NEVENT, NATTACH and so on.)
     * @return array
     */
    public function getClassConstants($className, $consType)
    {
        $oClass = new \ReflectionClass($className);
        $consts = $oClass->getConstants();
        $ans = [];
        foreach ($consts as $key => $value) {
            $strs = explode('_', $key);
            if ($strs[0] === $consType) {
                $ans[$key] = $value;
            }
        }
        return $ans;
    }

    /**
     * Gets all events from the all configured classes (which is in config folder)
     * @return array
     */
    public function getAllEvents()
    {
        $ans = [];
        foreach ($this->models as $model) {
            foreach ($this->getClassConstants($model, "NEVENT") as $eventKey => $eventValue) {
                $ans[$model . ' - ' . $eventKey] = $eventValue;
            }
        }
        return $ans;
    }

    /**
     * gather values of the all events in one array
     * @return array
     */
    public function getEventsRange()
    {
        $ans = [];
        foreach ($this->getAllEvents() as $eventKey => $eventValue) {
            $ans[] = $eventValue;
        }
        return $ans;
    }


    /**
     * get types of notification in array like array[value] = key
     * @return array
     */
    public function getNotiTypes()
    {
        return array_flip($this->getClassConstants(__CLASS__, "TYPE"));
    }

    /**
     * Array of notificiation types will be converted into string with names of events
     * divided by comma.
     * @param $array
     * @return string
     */
    public function toStringArrayType($array)
    {
        $map = $this->getNotiTypes();
        $ans = [];
        foreach ($array as $e) {
            $ans[] = $map[$e];
        }

        return implode(', ', $ans);
    }

    /**
     * Getting types of recipient for notify form
     * @param $eventValue
     * @return array
     */
    public function getRecipientTypes($eventValue)
    {
        $attach = $this->getAttach($eventValue);
        if(isset($attach['target']))
        {
            $target = $attach['target'];
            return $target ? [
                ['id' => self::RECIPIENT_CERTAIN, 'name' => self::RECIPIENT_CERTAIN],
                ['id' => self::RECIPIENT_ALL, 'name' => self::RECIPIENT_ALL],
            ] : [['id' => self::RECIPIENT_ALL, 'name' => self::RECIPIENT_ALL]];
        }
        return [];
    }


    /**
     * Getting all atributes of classes that defined on NATTACH events
     * format : [['Post' , [attrs]],['Post' , [attrs]],['Post' , [attrs]]]
     * @param $eventValue
     * @return array
     */
    public function getAttachTokens($eventValue)
    {
        $ans = [];
        $class = 'app\models\User';
        $obj = new $class();
        $item = ['Recipient', $obj->attributes()];
        $ans[] = $item;
        $attach = $this->getAttach($eventValue);
        if(isset($attach['attach']))
        {
            foreach ($attach['attach'] as $class) {
                $obj = new $class();
                $name = explode('\\', $class);
                $item = [$name[count($name) - 1], $obj->attributes()];
                $ans[] = $item;
            }
        }
        return $ans;
    }

    /**
     * Getting NATTACH constant of input event.
     * @param $eventValue
     * @return mixed
     */
    public function getAttach($eventValue)
    {
        $allEvents = array_flip($this->getAllEvents());
        if(isset($allEvents[$eventValue]))
        {
            $eventKey = $allEvents[$eventValue];
            $eventKey = explode(' - ', $eventKey);
            $attachments = $this->getClassConstants($eventKey[0], "NATTACH");
            $eventKey[1] = str_replace('NEVENT', 'NATTACH', $eventKey[1]);
            return $attachments[$eventKey[1]];
        }
        else
        {
            return null;
        }
    }
}