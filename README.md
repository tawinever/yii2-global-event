Yii2 - Global Notification Event Handler
========================================

This is small Yii2 - basic app with global notification handler component.

Installation
------------

1. Clone repo
2. Create db 'rgk'
3. Run composer global require "fxp/composer-asset-plugin:~1.1.1" 
4. Run composer update
5. Run yii migrate

And You are ready to work.

Demonstration
-------------
    
   1. Login to system as admin/admin
   2. Create Notification type NEVENT_RAUAN
   3. IMPORTANT : Open tab Users from the nav menu.
        
        It will trigger event NEVENT_RAUAN so that you will get 
        notifications which were chosen in creation process of notify.
   
   4. To see browser notifications go to main page. 



Under Hood
----------

1. To include model to Notification component, you should write full path of model class in configs.
    
        You can see example in app\config\web\ => notiHandler => models
2. Events that will be handled by component should be 
       
       1. Defined as const NEVENT_%eventName%
       2. Also you should specify data that will be sent with event in NATTACH_%eventName%
       3. NATTACH is const array with 2 keys 'target' and 'attach'
        
            If in trigger you are sending 'User' object in NotificationEvent->target then NATTACH['target'] = true 
            In other case NATTACH['target'] => false
            NATTACH['attach'] - Array with full name of models that were sent in trigger.
         

3. In trigger we use class NotificationEvent to send data to handler. 
        
        Attributes:
            target  - User object (used when we are trying to send notification to certain user)
            attach - Array of objects (data sent with trigger)
           
4. To extend component to create Telegram notification or SMS notification you should do above steps.

        1. Create const TYPO_%something% in app\components\Notification
        2. Add block of code in method 'sendNotification' in app\components\Notification
            if(in_array(self::TYPE_%something%,$typeArray))
            {
                //$sender  - User object - sender
                //$target  - User object - target
                //$msg['title'] - notification title
                //$msg['fulltext'] - notification text
            }


Some Info
---------

There is missing feature of testing because ... writing abstract unit test was strange.

Here is my [CV](https://hh.kz/resume/f7d13300ff0283b0130039ed1f774976783279) 
