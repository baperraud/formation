//
//
//// pseudo de l'utilisateur
//var uId = '',
//// bouton d'envoi du message
//    button = document.getElementsByClassName('sii-chat-send')[0],
//// message à envoyer vers le serveur
//    messageInput = document.getElementsByName('sii-chat-message')[0],
//// bouton de soumission du pseudo
//    buttonUser = document.getElementsByClassName('sii-chat-login')[0],
//// div contenant les messages reçus par le serveur
//    contentMessage = document.getElementsByClassName('sii-chat-content')[0];
//
/**
 * Classe permettant d'initaliser la communication avec le serveur
 * et de gérer l'ensemble des événements liés au websocket
 * @param host string L'URL du serveur de socket
 */
var WebsocketClass = function (host) {
    /** @var  WebSocket this.socket
     * Instance de Websocket qui gérera les connexions avec le serveur
     */
    this.socket = new WebSocket(host);
    /** @var Élément du DOM utilisé comme console pour afficher des messages */
};

/* On étend la classe grâce au prototypage */
WebsocketClass.prototype = {

    /* Initialisation du websocket */
    initWebsocket: function () {
        var $this = this;
        this.socket.onopen = function () {
            $this._onOpenEvent(this);
        };
        this.socket.onmessage = function (e) {
            $this._onMessageEvent(e);
        };
        this.socket.onclose = function () {
            $this._onCloseEvent();
        };
        this.socket.onerror = function (error) {
            $this._onErrorEvent(error);
        };
        console.log('websocket init');
    },

    /* Gestion des événements soulevés */
    _onErrorEvent: function (err) {
        console.log(err);
    },
    _onOpenEvent: function (socket) {
        console.log('socket opened Welcome - status ' + socket.readyState);
    },
    _onMessageEvent: function (e) {
        e = JSON.parse(e.data);
        //if (e.comment.length > 0) e.comment = JSON.parse(e.comment);


        /* Traitements après réception de la réponse */


        //// On affiche le message
        //contentMessage.innerHTML =
        //    contentMessage.innerHTML +
        //    '> <strong>' + e.msg.from + '</strong> : ' +
        //    e.msg.message + '<br />';


        console.log('message event launched');
        console.log(e);
        //console.log(e.comment);
        //console.log(e.action);
    },
    _onCloseEvent: function () {
        console.log('websocket closed - server not running');
    },

    /* Fonction permettant d'envoyer un commentaire au serveur */
    sendComment: function ($form) {

        var msg = {
            pseudonym: $("[name='pseudonym']", $form).val(),
            email: $("[name='email']", $form).val(),
            contenu: $("[name='contenu']", $form).val()
        };

        var message = JSON.stringify(msg);


        this.socket.send('{"action":"ctrl/chat/out", "comment":' + JSON.stringify(message) + '}');
        console.log('websocket message send');
    }
};

///* Instanciation d'un objet WebsocketClass avec l'URL en paramètre */
//var web_socket = new WebsocketClass('ws://localhost:11345/phpwebsocket/server.php');


$(document).ready(function () {

    // Instanciation d'un objet WebsocketClass avec l'URL en paramètre
    //var web_socket = new WebsocketClass('ws://localhost:11345/phpwebsocket/server.php');
    var web_socket = new WebsocketClass('ws://localhost:11345/test.json');

    // Initialisation de la connexion vers le serveur de socket
    web_socket.initWebsocket();

    $(document).on("submit", ".insert_comment_form", function (event) {
        event.preventDefault();

        web_socket.sendComment(this);

    });


});



