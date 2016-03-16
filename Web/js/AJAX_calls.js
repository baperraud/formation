$(document).ready(function () {

    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    var $body = $("body");

    $(document).on({
        ajaxStart: function () {
            $body.addClass("loading");
        },
        ajaxStop: function () {
            $body.removeClass("loading");
        }
    });

    $(document).on("submit", ".insert_comment_form", function () {
        var $this = $(this),
            $comments_container = $('#comments_container');

        $.post(
            $comments_container.data('json'),
            {
                pseudonym: $('#pseudonym' + $this.data('id')).val(),
                email: $('#email' + $this.data('id')).val(),
                contenu: $('#contenu' + $this.data('id')).val(),
                last_comment: $comments_container.data('last_comment')
            },
            function (data) {
                var json_answer = jQuery.parseJSON(data);
                if (json_answer.errors_exists) {
                    $this.children('p.error').remove();
                    for (var i = 0; i < json_answer.errors.length; i++) {
                        $this.append('<p class="error">' + json_answer.errors[i] + '</p>');
                    }
                } else {
                    // On clean le formulaire et les messages d'erreur
                    $this.find("input[type=text], input[type=email], textarea").val("");
                    $this.children('p.error').remove();

                    // On génère les nouveaux commentaires
                    for (i = 0; i < json_answer.comments.length; i++) {
                        $comments_container.prepend(
                            '<fieldset id="commentaire-' + json_answer.comments[i]['id'] + '">' +
                            '<legend>' +
                            'Posté par <strong>' +
                            (json_answer.comments[i]['owner_type'] == 1
                                ? '<a href="' + json_answer.comments[i]['user'] + '">' + escapeHtml(json_answer.comments[i]['pseudonym']) + '</a>' : escapeHtml(json_answer.comments[i]['pseudonym']) + ' (visiteur)') +
                            '</strong>' +
                            ' le ' + json_answer.comments[i]['date'] +
                            (json_answer.comments[i]['write_access'] == true ? '- <a href="' + json_answer.comments[i]['update'] + '">Modifier</a> | <a href="' + json_answer.comments[i]['delete'] + '">Supprimer</a>' : '') +
                            '</legend>' +
                            '<p class="overflow_hidden">' + escapeHtml(json_answer.comments[i]['contenu']) + '</p>' +
                            '</fieldset>'
                        );
                    }

                    // On update l'id du dernier commentaire inséré
                    $comments_container.data('last_comment', json_answer.comments[0]['id']);

                    // On centre l'affichage sur le dernier commentaire inséré
                    var viewportHeight = jQuery(window).height(),
                        last_comment = $('#comments_container fieldset:first'),
                        elHeight = last_comment.height(),
                        elOffset = last_comment.offset();
                    jQuery(window).scrollTop(elOffset.top + (elHeight / 2) - (viewportHeight / 2));
                }
            }
            ,
            'text'
        );

        return false;
    })
    ;

})
;

