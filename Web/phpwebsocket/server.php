#!/php -q
<?php
// Run from command prompt > php -q server.php
include "websocket.class.php";

// Extended basic WebSocket as ChatBot
class ChatBot extends WebSocket {
    public function process($user, $msg) {

        $this->say("< " . $user->socket . " :" . $msg);


        foreach ($this->users as $utilisateur) {
            $this->send($utilisateur->socket, $msg);
        }
    }
}

$master = new ChatBot("localhost", 11345);