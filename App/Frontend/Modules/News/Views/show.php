<?php
/**
 * @var \Entity\News $News
 * @var \Entity\Comment $Comment
 * @var array $comment_news_url
 * @var array $comment_update_url_a
 * @var array $comment_delete_url_a
 * @var array $comment_user_url_a
 * @var string $news_user_url
 */

use FormBuilder\CommentFormBuilder;
use OCFram\Session;

?>

<p>Par <em><a href="<?= $news_user_url ?>"><?= htmlspecialchars($News['auteur']) ?></a></em>,
    le <?= $News['Date_ajout']->format('d/m/Y à H\hi') ?></p>

<h2 class="overflow_hidden"><?= htmlspecialchars($News['titre']) ?></h2>

<p class="overflow_hidden"><?= htmlspecialchars($News['contenu']) ?></p>

<?php if ($News['Date_ajout'] != $News['Date_modif']): ?>
    <p style="text-align: right;">
        <small><em>Modifiée le <?= $News['Date_modif']->format('d/m/Y à H\hi') ?></em></small>
    </p>
<?php endif; ?>

<?php
$Form_builder = new CommentFormBuilder(new \Entity\Comment());
$Form_builder->build();
$Form = $Form_builder->getForm();
?>

<div id="insert_comment_form_container_top" class="insert_comment_form_container">
    <h3>Insérer un commentaire :</h3>
    <form action="<?= $comment_news_url ?>" method="post">
        <p>
            <?= $Form->createView() ?>

            <input type="submit" value="Commenter"/>
        </p>
    </form>
</div>

<?php
if (empty($Comment_a)): ?>
    <p>Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
<?php else:
    foreach ($Comment_a as $Comment): ?>
        <fieldset id="<?= 'commentaire-' . $Comment['id'] ?>">
            <legend>
                Posté par <strong>
                    <?php
                    // Si l'auteur du commentaire est un membre
                    if (!empty($comment_user_url_a[$Comment['id']])) {
                        echo '<a href=', $comment_user_url_a[$Comment['id']], '>', htmlspecialchars($Comment['pseudonym']), '</a>';
                    } else {
                        echo htmlspecialchars($Comment['pseudonym']), ' (visiteur)';
                    }
                    ?></strong>
                le <?= $Comment['Date']->format('d/m/Y à H\hi') ?>
                <?php if (Session::isAdmin()
                    || $Comment['pseudonym'] === Session::getAttribute('pseudo')
                ): ?> -
                    <a href=<?= $comment_update_url_a[$Comment['id']] ?>>Modifier</a> |
                    <a href=<?= $comment_delete_url_a[$Comment['id']] ?>>Supprimer</a>
                <?php endif; ?>
            </legend>
            <p class="overflow_hidden"><?= htmlspecialchars($Comment['contenu']) ?></p>
        </fieldset>
        <?php
    endforeach;
endif; ?>

<div id="insert_comment_form_container_bottom" class="insert_comment_form_container">
    <h3>Insérer un commentaire :</h3>
    <form action="<?= $comment_news_url ?>" method="post">
        <p>
            <?= $Form->createView() ?>

            <input type="submit" value="Commenter"/>
        </p>
    </form>
</div>

<script>

    //    $(document).ready(function () {
    //
    //        // On génère un formulaire
    //        $(".add_comment").click(function (e) {
    //            $(".form_container").empty();
    //
    //            $.get(
    //                'http://' + document.domain + '<?//= $comment_news_url ?>//' + '?f=json',
    //                false,
    //                function (data) {
    //
    //                    //var obj = jQuery.parseJSON(data);
    //
    //                    //alert(obj);
    //
    //                    if ($(e.target).is('.add_comment:first')) {
    //                        $(".form_container").first().html(data);
    //                        $(document).scrollTop( $(".add_comment").first().offset().top );
    //                    }
    //                    else {
    //                        $(".form_container").last().html(data);
    //                        $(document).scrollTop( $(".add_comment").last().offset().top );
    //
    //                    }
    //                },
    //                'text'
    //            );
    //        });
    //
    //
    //        $(document).on("submit", "#submit", function () {
    //            alert('click');
    //            return false;
    //            $.post(
    //                'http://' + document.domain + '<?//= $comment_news_url ?>//' + '?f=json',
    //                {
    //                    pseudonym: $("#pseudonym").val(),
    //                    email: $("#email").val(),
    //                    contenu: $("#contenu").val()
    //                },
    //
    //                function (data) {
    //                    alert(data);
    //                },
    //                'text'
    //            );
    //
    //
    //
    //        });
    //
    //    });

</script>