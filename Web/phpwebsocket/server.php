#!/php -q
<?php
// Run from command prompt > php -q server.php
include "websocket.class.php";

date_default_timezone_set('UTC');

/**
 * Class ChatBot
 * Héritage et implémentation de la classe WebSocket dans l'exemple d'un chat
 */
class ChatBot extends WebSocket {
    /**
     * Implémentation qui permet ici l'envoi du message (des données)
     * à chaque utilisateur connecté sur le serveur de socket (et sur le même socket)
     * @param $user UserSocket L'émetteur du message
     * @param $msg string Le message à transmettre
     */
    public function process($user, $msg) {

        $this->say("< " . $user->socket . " :" . $msg);


        foreach ($this->users as $utilisateur) {
            $this->send($utilisateur->socket, $msg);
        }
    }
}

$master = new ChatBot("localhost", 11345);