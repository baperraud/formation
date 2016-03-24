// Initialisation des variables globales
var $body = $("body"),
    $window = $(window),
    load_active = true,
    $comments_container = $("#comments_container"),
    post_comment_lock = false,
    load_comments_on_scroll_lock = false,
    refresh_lock = false;

$(document).ready(function () {

    // Fonction de test pour trigger deux submit (double click)
    //setTimeout(function () {
    //    $("#insert_comment_form_container_top").find("input[type='submit']").trigger( "click" );
    //    $("#insert_comment_form_container_top").find("input[type='submit']").trigger( "click" );
    //}, 5000);


    /*
     Actions relatives aux requêtes AJAX en cours : logo de chargement...
     */
    //noinspection JSUnusedGlobalSymbols,JSUnusedGlobalSymbols,JSUnusedGlobalSymbols
    $(document).on({
        ajaxStart: function () {
            setTimeout(function () {
                //noinspection JSUnresolvedVariable
                if ($.active > 0) $body.addClass("loading");
            }, 250);
        },
        ajaxStop: function () {
            $body.removeClass("loading");
        },
        ajaxError: function () {
            $body.removeClass("loading");
        }
    });

    // Délai de rafraichissement (en ms)
    const REFRESH_TIMOUT = 5000;

    // Configuration par défaut des requêtes AJAX
    $.ajaxSetup({
        dataType: "json",
        timeout: REFRESH_TIMOUT
    });

    /* Paramètres par défaut pour les notifications */
    //noinspection JSUnresolvedVariable
    $.notify.defaults({
        className: "success",
        elementPosition: "right middle",
        globalPosition: "right middle",
        autoHideDelay: 5000
    });


    /* Centrage de l'affichage sur le commentaire de l'url si existant */
    var sharpPos = window.location.href.lastIndexOf("#"),
        dashPos = window.location.href.lastIndexOf("-"),
        id = window.location.href.substring(dashPos + 1);

    if (sharpPos >= 0) news_loadCommentsUntilOneFound(id);


    //// Instanciation d'un objet WebsocketClass avec l'URL en paramètre
    //var web_socket = new WebsocketClass('ws://localhost:11345/phpwebsocket/server.php');
    //
    //// Initialisation de la connexion vers le serveur de socket
    //web_socket.initWebsocket();


    /* Requête AJAX pour l'envoi du formulaire (poster un commentaire) */
    $(document).on("submit", ".insert_comment_form", function (event) {
        if (post_comment_lock) return false;
        // On verrouille le posting
        post_comment_lock = true;

        event.preventDefault();

        var $this = $(this), jqXHR;

        // On lance la requête
        jqXHR = $.post(
            $this.data('ajax'),
            {
                pseudonym: $("[name='pseudonym']", $this).val(),
                email: $("[name='email']", $this).val(),
                contenu: $("[name='contenu']", $this).val(),
                last_comment: $comments_container.find('fieldset:first').data('id')
            });

        /*
         En cas de réussite
         */
        //noinspection JSCheckFunctionSignatures
        /**
         * Fonction qui génère les nouveaux commentaires ayant été postés
         * depuis le chargement de la page
         * @param data.code Vaut 1 si le formulaire contenait des erreurs
         * @param data.error_a Le tableau des erreurs
         * @param data.content_a.comments_html Les fieldsets des commentaires en html
         */
        jqXHR.done(function (data) {
            console.log(data);
            // Le commentaire n'a pas été enregistré en BDD
            if (data.code == 1) {
                $this.find('p.error').remove();
                for (var i = 0; i < data.error_a.length; i++) {
                    $("[name='" + data.error_a[i].name + "']", $this).after(
                        $('<p></p>').addClass("error").text(data.error_a[i].message)
                    );
                }
            }
            // Le commentaire a bien été enregistré en BDD
            else {
                // On clean les formulaires et les messages d'erreur
                $('.insert_comment_form').each(function () {
                    this.reset();
                });
                $this.find('p.error').remove();

                // On retire le message 'Aucun commentaire...' si nécessaire
                if (!$comments_container.find('fieldset').length)
                    $('#no_comment_alert').hide();


                // On force un rafraichissement
                news_refreshComments(true, true);


                ///* Envoi du commentaire via websocket */
                //web_socket.sendComment(data.content_a.comments[0]);
            }

            removeFlash();
        });

        /*
         En cas d'erreur
         */
        //noinspection JSCheckFunctionSignatures
        jqXHR.fail(function () {
            //noinspection JSUnresolvedFunction
            $.notify("Erreur de l'ajout du commentaire,\nveuillez réessayer", "error");
        });

        /*
         Dans tous les cas
         */
        //noinspection JSCheckFunctionSignatures
        jqXHR.always(function () {
            post_comment_lock = false;
        });
    });


    /* Requête AJAX pour l'affichage des anciens commentaires en scrollant */
    $window.scroll(function () {

        // S'il reste des commentaires à charger
        if (load_active) {

            // Si le dernier commentaire chargé est visible
            if (news_isOnScreen($comments_container.find('fieldset:last'))) {

                if (load_comments_on_scroll_lock) return false;
                // On verrouille le load-on-scroll
                load_comments_on_scroll_lock = true;

                //noinspection JSCheckFunctionSignatures
                /**
                 * Fonction qui affiche les commentaires en scrollant
                 * @param data La réponse JSON récupérée
                 * @param data.content_a.comments_count Le nombre de commentaires récupérés
                 */

                news_loadOldComments().done(function (data) {
                    news_buildOldComments(data);
                    news_stopLoadingComments(data);

                    // Si l'on a chargé des commentaires
                    if (data.content_a.comments_count) {
                        //noinspection JSUnresolvedFunction
                        $.notify(data.content_a.comments_count +
                            (data.content_a.comments_count == 1 ?
                                " commentaire plus ancien a été chargé !" :
                                " commentaires plus anciens ont été chargés" ),
                            "info");
                    }

                    load_comments_on_scroll_lock = false;
                });
            }
        }
    });


    /* Requête AJAX de rafraichissement à intervalles de temps réguliers */
    window.setInterval(news_refreshComments, REFRESH_TIMOUT);


    var $bottom_form = $('#insert_comment_form_bottom'),
        $top_form = $('#insert_comment_form_top');

    /* On modifie le formulaire du bas pour ne pas avoir de conflits d'ids */
    $bottom_form.find("label[for='pseudonym']").attr('for', 'pseudonymBottom');
    $bottom_form.find("input[id='pseudonym']").attr('id', 'pseudonymBottom');
    $bottom_form.find("label[for='email']").attr('for', 'emailBottom');
    $bottom_form.find("input[id='email']").attr('id', 'emailBottom');
    $bottom_form.find("label[for='contenu']").attr('for', 'contenuBottom');
    $bottom_form.find("textarea[id='contenu']").attr('id', 'contenuBottom');


    /* Synchronisation des deux formulaires */
    $(".insert_comment_form").on('input', function () {
        if ($(this).is($top_form)) {
            $bottom_form.find("input[name='pseudonym']").val($(this).find("input[name='pseudonym']").val());
            $bottom_form.find("input[name='email']").val($(this).find("input[name='email']").val());
            $bottom_form.find("textarea[name='contenu']").val($(this).find("textarea[name='contenu']").val());
        }
        else {
            $top_form.find("input[name='pseudonym']").val($(this).find("input[name='pseudonym']").val());
            $top_form.find("input[name='email']").val($(this).find("input[name='email']").val());
            $top_form.find("textarea[name='contenu']").val($(this).find("textarea[name='contenu']").val());
        }
    });
});

/**
 * Fonction permettant de savoir si un commentaire existe dans le DOM
 * @param id L'id (html) du commentaire dont on souhaite vérifier l'existence
 * @returns {boolean}
 */
function news_commentExists(id) {
    return $('#comments_container').find("[data-id='" + id + "']").length != 0;
}

/**
 * Fonction permettant de charger les commentaires jusqu'à un commentaire précis
 * @param id L'id du commentaire que l'on veut charger
 */
function news_loadCommentsUntilOneFound(id) {

    //noinspection JSCheckFunctionSignatures
    news_loadOldCommentsWithinRange(id, $comments_container.find('fieldset:last').data('id')).done(function (data) {

        news_buildOldComments(data);

        /* On charge quand même les 15 commentaires qui le précèdent pour éviter
         un chargement automatique lors du scrolling
         */
        //noinspection JSCheckFunctionSignatures
        news_loadOldComments().done(function (data) {
            news_buildOldComments(data);
            /* Si l'on a renvoyé moins de 15 commentaires,
             alors il n'y en a plus à charger */
            if (data.content_a.comments_count < $comments_container.data('limit'))
                load_active = false;
        });

        centerViewportToElem($comments_container.find('fieldset:last'));
    });
}

/**
 * Fonction permettant d'exécuter une requête AJAX qui charge
 * les prochains commentaires plus anciens
 * @returns JQueryXHR
 */
function news_loadOldComments() {
    return $.post({
        url: $comments_container.data('load'),
        data: {
            last_comment: $comments_container.find('fieldset:last').data('id'),
            type: 'old'
        }
    });
}

/**
 * Fonction permettant d'exécuter une requête AJAX qui charge
 * d'anciens commentaires entre deux bornes
 * @param $first_comment_id L'id du premier commentaire (borne inf)
 * @param $last_comment_id L'id du dernier commentaire (borne sup)
 * @returns JQueryXHR
 */
function news_loadOldCommentsWithinRange($first_comment_id, $last_comment_id) {
    return $.post({
        url: $comments_container.data('load'),
        data: {
            first_comment: $first_comment_id,
            last_comment: $last_comment_id,
            type: 'range'
        }
    });
}

/**
 * Fonction permettant de construire d'anciens commentaires récupérés via AJAX
 * @param data La réponse JSON récupérée
 * @param data.content_a.comments_html Les fieldsets des commentaires en html
 * @see news_loadOldComments
 */
function news_buildOldComments(data) {
    $comments_container.append($(data.content_a.comments_html).hide().fadeIn());
}

/**
 * Fonction permettant de construire de nouveaux commentaires récupérés via AJAX
 * @param data La réponse JSON récupérée
 * @param data.content_a.comments_html Les fieldsets des commentaires en html
 * @param data.content_a.comments_count Le nombre de commentaires récupérés
 * @param add Booléan, vaut true si l'on a posté un commentaire
 */
function news_buildNewComments(data, add) {
    $comments_container.prepend($(data.content_a.comments_html).hide().fadeIn());

    var count = data.content_a.comments_count;

    if (data.content_a.comments_count) {

        // Si l'on construit aussi un commentaire que l'on vient de poster
        if (add) {
            count--;
            // On centre l'affichage sur le dernier commentaire inséré
            var $last_comment = $comments_container.find('fieldset:first');
            centerViewportToElem($last_comment);
            $last_comment.hide().show(300);

            //noinspection JSUnresolvedFunction
            $.notify("Le commentaire a bien été inséré !");
        }

        if (count) {
            //noinspection JSUnresolvedFunction
            $.notify(count + (count == 1 ?
                    " nouveau commentaire a été chargé !" :
                    " nouveaux commentaires ont été chargés !"),
                "info");
        }
    }
}

/**
 * Fonction permettant de désactiver le chargement
 * des anciens commentaires lors du scrolling
 * @param data La réponse JSON récupérée
 * @param data.content_a.comments_count Le nombre de commentaires récupérés
 */
function news_stopLoadingComments(data) {
    if (data.content_a.comments_count < $comments_container.data('limit'))
        load_active = false;
}

var _refresh_comment_counter = 0;

function news_refreshComments(forced, add) {
    if (refresh_lock && !forced) return false;
    // On verrouille le posting
    refresh_lock = true;

    var current_counter_refresh = ++_refresh_comment_counter;

    var first_returned = false;
    var second_returned = false;

    /*
     On affiche les nouveaux commentaires
     */
    $.post(
        $comments_container.data('load'),
        {
            last_comment: $comments_container.find('fieldset:first').data('id')
        },
        /**
         * Fonction qui génère les nouveaux commentaires ayant été postés
         * depuis le chargement de la page
         * @param data La réponse JSON récupérée
         * @param data.content_a.comments_html Les fieldsets des commentaires en html
         * @param data.content_a.comments_count Le nombre de commentaires récupérés
         */
        function (data) {
            if (current_counter_refresh != _refresh_comment_counter) return false;
            first_returned = true;
            refresh_lock = !(first_returned && first_returned);
            // On génère les nouveaux commentaires
            news_buildNewComments(data, add);
        }
    );

    /*
     On efface les commentaires ayant été supprimés
     */
    // On commence par récupérer les ids des commentaires actuellement construits
    var $comment_a = $comments_container.find('fieldset'),
        $comment_id_a = [];
    for (var i = 0; i < $comment_a.length; i++) {
        $comment_id_a.push($($comment_a[i]).data('id'));
    }
    $.post(
        $comments_container.data('get_deleted'),
        {
            comments: $comment_id_a
        },
        /**
         * Fonction permettant d'effacer du DOM les commentaires supprimés
         * depuis le chargement de la page
         * @param data La réponse JSON récupérée
         * @param data.content_a.deleted Tableau des commentaires ayant été supprimés
         */
        function (data) {
            if (current_counter_refresh != _refresh_comment_counter) return false;
            second_returned = true;
            refresh_lock = !(first_returned && first_returned);
            // On retire les commentaires qui ont été supprimés
            for (var i = 0; i < data.content_a.deleted.length; i++) {
                var $comment_to_remove = $comments_container.find("[data-id='" + data.content_a.deleted[i] + "']");
                $comment_to_remove.hide(300, function () {
                    this.remove();
                });
            }

            if (data.content_a.deleted.length) {
                //noinspection JSUnresolvedFunction
                $.notify(data.content_a.deleted.length +
                    (data.content_a.deleted.length == 1 ?
                        " commentaire vient d'être supprimé !" :
                        " commentaires viennent d'être supprimés !"),
                    "info");
            }

            // On charge les 15 prochains commentaires s'il y en a moins
            if ($comments_container.find("fieldset").length < 15) {
                //noinspection JSCheckFunctionSignatures
                news_loadOldComments().done(function (data) {
                    news_buildOldComments(data);
                    news_stopLoadingComments(data);
                });
            }
        }
    );

}