Yii2 - Global Notification Event Handler
========================================

This is small Yii2 - basic app with global notification handler component.

Installation
------------

1. Clone repo
2. Create db 'rgk'
3. Run composer update
4. Run yii migrate

And You are ready to work.


Under Hood
----------

1. To include model to Notification component, you should write full path of model class in configs.
    You can see example in app\config\web\ => notiHandler => models
2. Events that will be handled by component should be defined as const NEVENT_%eventName%
    Also you should specify data that will be sent with event in NATTACH_%eventName%
    
    NATTACH is array with 2 keys 'target' and 'attach'
    
        If in trigger you are sending 'User' object 'target' = true
         
         In other case 'target' => false
         
         NATTACH['attach'] - Array with full name of models that were sent in trigger.

3. In trigger we use class NotificationEvent to send data to handler. 
        Attributes:
            target  - User object (used when we are trying to send notification to certain user)
            attach - Array of objects (data sent with trigger)
            
Some Info
---------

There is missing feature of testing because ... writing unit test for CRUD was too strange.

Here is my [CV](https://hh.kz/resume/f7d13300ff0283b0130039ed1f774976783279)
