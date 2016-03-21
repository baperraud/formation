// Initialisation des variables globales
var $body = $("body"),
    $window = $(window),
    $load_active = true,
    $comments_container = $("#comments_container"),
    timer;

$(document).ready(function () {

    // On active le logo de chargement en cas de requête AJAX en cours
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

    // Paramètres par défaut pour les notifications
    //noinspection JSUnresolvedVariable
    $.notify.defaults({
        className: "success",
        elementPosition: "right middle",
        globalPosition: "right middle",
        autoHideDelay: 5000
    });


    //TODO: ne faire qu'une requête SQL directe (pas de récursion)
    /* Centrage de l'affichage sur le commentaire de l'url si existant */
    var sharpPos = window.location.href.lastIndexOf("#"),
        anchor = window.location.href.substring(sharpPos + 1);

    if (sharpPos >= 0) news_loadCommentsUntilOneFound(anchor);


    /* Requête AJAX pour l'envoi du formulaire (poster un commentaire) */
    $(document).on("submit", ".insert_comment_form", function (event) {

        event.preventDefault();

        var $this = $(this), jqxhr;

        // On lance la requête
        jqxhr = $.post(
            $this.data('ajax'),
            {
                pseudonym: $("[name='pseudonym']", $this).val(),
                email: $("[name='email']", $this).val(),
                contenu: $("[name='contenu']", $this).val(),
                last_comment: $comments_container.find('fieldset:first').data('id')
            });

        // En cas de réussite
        //noinspection JSCheckFunctionSignatures
        jqxhr.done(
            /**
             * Fonction qui génère les nouveaux commentaires ayant été postés
             * depuis le chargement de la page
             * @param data La réponse JSON récupérée
             * @param data.errors_exists Booléan, vaut true si le formulaire contient des erreurs
             * @param data.errors Tableau contenant les erreurs de formulaire
             * @param data.comments Tableau contenant les nouveaux commentaires à afficher
             */
            function (data) {
                if (data.errors_exists) {
                    $this.children('p.error').remove();
                    for (var i = 0; i < data.errors.length; i++) {
                        $this.append('<p class="error">' + data.errors[i] + '</p>');
                    }
                } else {
                    // On clean le formulaire et les messages d'erreur
                    $this.find("input[type=text], input[type=email], textarea").val("");
                    $this.children('p.error').remove();

                    // On retire le message 'Aucun commentaire...' si c'est le 1er
                    if ($comments_container.data('last_comment') == 0)
                        $('#no_comment_alert').remove();

                    var $last_comment;
                    // On génère les nouveaux commentaires
                    var $comments_a = data.comments.reverse();
                    for (i = 0; i < $comments_a.length; i++) {
                        $last_comment = news_buildCommentHTML($comments_a[i]);
                        // Si le commentaire n'existe pas déjà
                        if (!news_commentExists($last_comment.data('id')))
                            $comments_container.prepend($last_comment);
                    }

                    // On centre l'affichage sur le dernier commentaire inséré
                    centerViewportToElem($last_comment);
                    $last_comment.hide().show(300);

                    //noinspection JSUnresolvedFunction
                    $.notify("Le commentaire a bien été inséré !");
                }
            });

        // En cas d'erreur
        //noinspection JSCheckFunctionSignatures
        jqxhr.fail(function () {
            //noinspection JSUnresolvedFunction
            $.notify("Erreur de l'ajout du commentaire,\nveuillez réessayer !", "error");
            jqxhr.abort();
        });

        removeFlash();
    });


    /* Requête AJAX pour l'affichage des anciens commentaires en scrollant */
    $window.scroll(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {

            // S'il reste des commentaires à charger
            if ($load_active) {

                // Si le dernier commentaire chargé est visible
                if (news_isOnScreen($comments_container.find('fieldset:last'))) {

                    //noinspection JSCheckFunctionSignatures
                    news_loadOldComments().done(function (data) {

                        news_generateOldComments(data);

                        // Si l'on a chargé des commentaires
                        if (data.comments.length) {
                            //noinspection JSUnresolvedFunction
                            $.notify(data.comments.length + " commentaires plus anciens ont été chargés !", "info");
                        }
                    });
                }
            }

        }, 250);
    });


    /* Requête AJAX de rafraichissment */
    window.setInterval(function () {

        // On affiche les nouveaux commentaires
        $.post(
            $comments_container.data('load_new'),
            {
                last_comment: $comments_container.find('fieldset:first').data('id')
            },
            function (data) {
                // On génère les nouveaux commentaires
                var $comments_a = data.comments.reverse();
                for (i = 0; i < $comments_a.length; i++) {
                    var $last_comment = news_buildCommentHTML($comments_a[i]);
                    // Si le commentaire n'existe pas déjà
                    if (!news_commentExists($last_comment.data('id')))
                        $comments_container.prepend($last_comment.hide().fadeIn());
                }

                if (data.comments.length) {
                    //noinspection JSUnresolvedFunction
                    $.notify(data.comments.length + (data.comments.length == 1 ? " nouveau commentaire a été chargé !" : " nouveaux commentaires ont été chargés !"), "info");
                }
            }
        );

        // On efface les commentaires ayant été supprimé
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
             * @param data.deleted Tableau des commentaires ayant été supprimés
             */
            function (data) {
                // On retire les commentaires qui ont été supprimé
                for (var i = 0; i < data.deleted.length; i++) {
                    //$comments_container.find("[data-id='" + data.deleted[i] + "']").remove();
                    var $comment_to_remove = $comments_container.find("[data-id='" + data.deleted[i] + "']");
                    $comment_to_remove.hide(300, function () {
                        this.remove();
                    });
                }

                if (data.deleted.length) {
                    //noinspection JSUnresolvedFunction
                    $.notify(data.deleted.length + (data.deleted.length == 1 ? " commentaire vient d'être supprimé !" : " commentaires viennent d'être supprimés !"), "info");
                }

                // On charge les 15 prochains commentaires s'il y en a moins
                if ($comments_container.find("fieldset").length < 15) {
                    //noinspection JSCheckFunctionSignatures
                    news_loadOldComments().done(function (data) {
                        news_generateOldComments(data);
                    });
                }
            }
        );

    }, REFRESH_TIMOUT);


    /* On modifie le formulaire du bas pour ne pas avoir de conflits d'ids */
    var $bottom_form = $('#insert_comment_form_bottom');
    $bottom_form.find("label[for='pseudonym']").attr('for', 'pseudonymBottom');
    $bottom_form.find("input[id='pseudonym']").attr('id', 'pseudonymBottom');
    $bottom_form.find("label[for='email']").attr('for', 'emailBottom');
    $bottom_form.find("input[id='email']").attr('id', 'emailBottom');
    $bottom_form.find("label[for='contenu']").attr('for', 'contenuBottom');
    $bottom_form.find("textarea[id='contenu']").attr('id', 'contenuBottom');
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
 * Fonction permettant de construire la représentation HTML d'un commentaire (DOM)
 * @param comment Le commentaire à construire
 * @param comment.id L'id du commentaire
 * @param comment.date La date du commentaire
 * @param comment.contenu Le contenu du commentaire
 * @param comment.user Le lien vers le profil du membre
 * @param comment.pseudonym Le pseudo de l'auteur
 * @param comment.update Le lien pour modifier le commentaire
 * @param comment.delete Le lien pour supprimer le commentaire
 * @param comment.owner_type Type de l'auteur du commentaire
 *          1 si l'auteur est membre
 *          2 si c'est un visiteur
 * @param comment.write_access Booléan
 *          true si l'auteur a droit de modification du commentaire
 *          false sinon
 * @returns {JQuery|jQuery}
 */
function news_buildCommentHTML(comment) {
    var user = '';
    if (comment.owner_type == 1)
        user = $('<a></a>')
            .attr('href', comment.user)
            .text(comment.pseudonym);
    else
        user = comment.pseudonym + ' (visiteur)';

    var edit_button = '';
    var delete_button = '';
    if (comment.write_access) {
        edit_button = $('<a></a>')
            .attr('href', comment.update)
            .text('Modifier');
        delete_button = $('<a></a>')
            .attr('href', comment.delete)
            .text('Supprimer');
    }

    return $('<fieldset></fieldset>')
        .attr('id', 'commentaire-' + comment.id)
        .attr('data-id', comment.id)
        .append(
            $('<legend></legend>')
                .append(
                    'Posté par ',
                    $('<strong></strong>')
                        .append(
                            user
                        ),
                    ' le ' + comment.date,
                    (comment.write_access) ? ' - ' : '',
                    edit_button,
                    (comment.write_access) ? ' | ' : '',
                    delete_button
                ),
            $('<p></p>')
                .addClass('overflow_hidden')
                .text(comment.contenu)
        );

}


/**
 * Fonction permettant de charger les commentaires jusqu'à ce qu'un spécifique le soit
 * @param anchor L'id (html) du commentaire que l'on veut chargé
 */
function news_loadCommentsUntilOneFound(anchor) {

    //noinspection JSCheckFunctionSignatures
    news_loadOldComments().done(function (data) {

        news_generateOldComments(data);

        // Si l'on n'a pas encore trouvé le commentaire, on recommence
        if (!$comments_container.find("fieldset[id='" + anchor + "']").length && $load_active)
            news_loadCommentsUntilOneFound(anchor);
        else
            var $theComment = $comments_container.find("fieldset[id='" + anchor + "']");
        centerViewportToElem($theComment);
    });
}

/**
 * Fonction permettant d'exécuter une requête AJAX qui charge
 * les prochains commentaires plus anciens
 * @returns JQueryXHR
 */
function news_loadOldComments() {
    return $.post({
        url: $comments_container.data('load_old'),
        data: {
            last_comment: $comments_container.find('fieldset:last').data('id'),
            type: 'old'
        }
    });
}

/**
 * Fonction permettant de générer les anciens commentaires ayant été récupérés via AJAX
 * @param data La réponse JSON récupérée
 * @see news_loadOldComments
 */
function news_generateOldComments(data) {
    // On génère les anciens commentaires
    for (var i = 0; i < data.comments.length; i++) {
        $comments_container.append($(news_buildCommentHTML(data.comments[i]).hide().fadeIn()));
    }

    /* Si l'on a renvoyé moins de 15 commentaires,
     alors il n'y en a plus à charger */
    if (data.comments.length < $comments_container.data('limit'))
        $load_active = false;
}
