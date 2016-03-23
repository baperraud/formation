<!DOCTYPE html>

<html>
<head>
    <meta charset="ISO-8859-1">
    <title>Insert title here</title>

    <style>
        .sii-chat {
            width: 400px;
            padding: 10px;
            background: #ccc;
            margin: 20px auto;
        }

        .sii-chat-content {
            min-height: 400px;
            max-height: 400px;
            overflow: hidden;
            overflow-y: scroll;
            background: #fff;
            box-shadow: 0 0 5px 0 #000;
            margin-bottom: 10px;
        }

        .console {
            min-height: 50px;
            max-height: 100px;
            overflow: hidden;
            overflow-y: scroll;
        }

        .sii-chat-form input[name="sii-chat-message"] {
            width: 300px;
            box-shadow: inset 0 0 5px 0 #000;
        }

        .sii-chat-form button {
            width: 80px;
            float: right;
            color: #ffffff;
            -moz-box-shadow: 0 0 5px #343434;
            -webkit-box-shadow: 0 0 5px #343434;
            -o-box-shadow: 0 0 5px #343434;
            box-shadow: 0 0 5px #343434;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border-radius: 5px;
            border: 1px solid #656565;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="34cdf9", endColorstr="3166ff"); /* Pour IE seulement et mode gradient à linear */
            background: -webkit-gradient(linear, left top, left bottom, from(#34cdf9), to(#3166ff));
            background: -moz-linear-gradient(top, #34cdf9, #3166ff);
        }

        .sii-chat-form button:active {
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="3166ff", endColorstr="34cdf9"); /* Pour IE seulement et mode gradient à linear */
            background: -webkit-gradient(linear, left top, left bottom, from(#3166ff), to(#34cdf9));
            background: -moz-linear-gradient(top, #3166ff, #34cdf9);
        }
    </style>


</head>
<body>
<div class="sii-chat"> <!-- conteneur du chat -->
    <div>Pseudo : <!--suppress HtmlFormInputWithoutLabel -->
        <input type="text" name="sii-chat-name"/>
        <button class="sii-chat-login">Valider</button>
    </div>
    <!-- pseudo à saisir pour le chat -->
    <div class="sii-chat-content"> <!-- les messages apparaitront ici -->
    </div>
    <div>
        <form class="sii-chat-form" onsubmit="return false;">
            <!--suppress HtmlFormInputWithoutLabel -->
            <input type="text" value="" name="sii-chat-message" disabled="disabled"/><!-- saisie du message à saisir -->
            <button class="sii-chat-send" disabled="disabled">ok</button>
            <!-- Bouton d'envoi du message saisi -->
        </form>
    </div>
    <div class="console"></div>
</div>
</body>
</html>

<script>
    // pseudo de l'utilisateur
    var uId = '',
    // bouton d'envoi du message
        button = document.getElementsByClassName('sii-chat-send')[0],
    // message à envoyer vers le serveur
        messageInput = document.getElementsByName('sii-chat-message')[0],
    // bouton de soumission du pseudo
        buttonUser = document.getElementsByClassName('sii-chat-login')[0],
    // div contenant les messages reçus par le serveur
        contentMessage = document.getElementsByClassName('sii-chat-content')[0];

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
        this.console = document.getElementsByClassName('console')[0];
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
            this.console.innerHTML = this.console.innerHTML + 'websocket init <br />';
        },

        /* Gestion des événements soulevés */
        _onErrorEvent: function (err) {
            console.log(err);
            this.console.innerHTML = this.console.innerHTML + 'websocket error <br />';
        },
        _onOpenEvent: function (socket) {
            console.log('socket opened');
            this.console.innerHTML = this.console.innerHTML + 'socket opened Welcome - status ' + socket.readyState + '<br />';
        },
        _onMessageEvent: function (e) {
            e = JSON.parse(e.data);
            if (e.msg.length > 0) e.msg = JSON.parse(e.msg);

            // On affiche le message
            contentMessage.innerHTML =
                contentMessage.innerHTML +
                '> <strong>' + e.msg.from + '</strong> : ' +
                e.msg.message + '<br />';

            /* Scroll automatique vers le bas
             dans la div contenant la réception des messages */
            contentMessage.scrollTop = contentMessage.scrollHeight;

            this.console.innerHTML = this.console.innerHTML + 'message event launched <br />';
        },
        _onCloseEvent: function () {
            console.log('connection closed');
            this.console.innerHTML = this.console.innerHTML + 'websocket closed - server not running<br />';
            uId = '';
            document.getElementsByName('sii-chat-name')[0].value = '';
            messageInput.disabled = 'disabled';
            button.disabled = 'disabled';
        },

        /* Fonction permettant d'envoyer des messages */
        sendMessage: function () {
            var message = '{"from":"' + uId + '", "message":"' + messageInput.value + '"}';
            this.socket.send('{"action":"ctrl/chat/out", "msg":' + JSON.stringify(message) + '}');
            messageInput.value = '';
            this.console.innerHTML = this.console.innerHTML + 'websocket message send <br />';
        }
    };

    /* Instanciation d'un objet WebsocketClass avec l'URL en paramètre */
    var web_socket = new WebsocketClass('ws://localhost:11345/phpwebsocket/server.php');


    /* Mise en place du mécanismes de de listeners d'events */
    if (button.addEventListener) {
        /* En cas de click sur le bouton pour valider son pseudo */
        buttonUser.addEventListener('click', function (e) {
            e.preventDefault();

            // Initialisation de la connexion vers le serveur de socket
            web_socket.initWebsocket();
            // Récupération de la valeur du pseudo de l'utilisateur
            uId = document.getElementsByName('sii-chat-name')[0].value;

            // On permet l'accès au chat
            messageInput.disabled = '';
            button.disabled = '';
        }, true);
        /* En cas de click sur le bouton pour envoyer le message */
        button.addEventListener('click', function (e) {
            e.preventDefault();

            // Envoi du message vers le serveur
            web_socket.sendMessage();
        }, true);
    }
    else console.log('votre navigateur n\'accepte pas le addevenlistener');


</script>