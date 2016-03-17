<?php
/**
 * @var \Entity\News $News
 * @var \Entity\Comment[] $Comment_a
 * @var array $comment_news_url_a
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
$Form_builder_top = new CommentFormBuilder(new \Entity\Comment());
$Form_builder_top->build('Top');
$Form_top = $Form_builder_top->getForm();
$Form_builder_bottom = new CommentFormBuilder(new \Entity\Comment());
$Form_builder_bottom->build('Bottom');
$Form_bottom = $Form_builder_bottom->getForm();
?>

<div id="insert_comment_form_container_top" class="insert_comment_form_container">
    <h3>Insérer un commentaire :</h3>
    <form id="insert_comment_form_top" class="insert_comment_form" action="<?= $comment_news_url_a['html'] ?>" data-id="Top"
          method="post">
        <p>
            <?= $Form_top->createView() ?>

            <input type="submit" value="Commenter"/>

        </p>
    </form>
</div>

<div id="comments_container" data-json="<?= $comment_news_url_a['json'] ?>" data-last_comment="<?= empty($Comment_a) ? 0 : $Comment_a[0]['id'] ?>">

    <?php
    if (empty($Comment_a)): ?>
        <p id="no_comment_alert">Aucun commentaire n'a encore été posté. Soyez le premier à en laisser un !</p>
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

</div>

<div id="insert_comment_form_container_bottom" class="insert_comment_form_container">
    <h3>Insérer un commentaire :</h3>
    <form id="insert_comment_form_bottom" class="insert_comment_form" action="<?= $comment_news_url_a['html'] ?>" data-id="Bottom"
          method="post">
        <p>
            <?= $Form_bottom->createView() ?>

            <input type="submit" value="Commenter"/>
        </p>
    </form>
</div>

<div class="spinning_loading"></div>