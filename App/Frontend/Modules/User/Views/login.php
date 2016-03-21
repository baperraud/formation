<?php
/**
 * @var string $form
 * @var string $confirm_url
 */
?>

<h2>Connexion</h2>
<form action="<?= $confirm_url ?>" method="post">
    <p>
        <?= $form ?>

        <br />
        <input type="submit" value="Connexion"/>
    </p>
</form>