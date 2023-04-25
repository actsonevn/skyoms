<?php

namespace App\Http\Controllers;

use App\Events\NewMessageNotification;
use App\Events\SendMessage;
use Illuminate\Http\Request;

class MessageData {
    public $id;
    public $message;
    
    public function __construct($id, $message) {
      $this->id = $id;
      $this->message = $message;
    }
}


class MessageController extends Controller
{
    public function send()
    {
        
        // want to broadcast NewMessageNotification event 
        event(new NewMessageNotification('Test descoration'));
        
        // ... 
    }


    public function sendMessage()
    {
        
        $id = 1;
        $message = 'Hello broadcasting user 1';

        $messageData = new MessageData($id,  $message);
        // want to broadcast NewMessageNotification event 
        event(new SendMessage($messageData));
        
        // ... 
    }
}
