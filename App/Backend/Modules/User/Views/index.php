<?php
/**
 * @var int $nombre_membres
 * @var \Entity\User[] $User_a
 */
?>

<p style="text-align: center">Il y a actuellement <?= $nombre_membres ?> membres inscrits. En voici la liste :</p>

<table id="users_admin_view">
    <tr>
        <th>Pseudo</th>
        <th>E-mail</th>
        <th>Date d'inscription</th>
        <th>Rôle</th>
        <th>État</th>
    </tr>

    <?php
    foreach ($User_a as $User) {
        switch ($User['etat']) {
            case Model\UsersManager::COMPTE_INACTIF:
                $status = 'Inactif';
                break;
            case Model\UsersManager::COMPTE_ACTIF:
                $status = 'Actif';
                break;
        }

        switch ($User['role']) {
            case Model\UsersManager::ROLE_ADMIN:
                $role = 'Admin';
                break;
            case Model\UsersManager::ROLE_USER:
                $role = 'Membre';
                break;
        }

        echo '
				<tr>
					<td>', htmlspecialchars($User['pseudonym']), '</td>
					<td>', htmlspecialchars($User['email']), '</td>
					<td>', $User['Date']->format('d/m/Y'), '</td>
					<td>', $status, '</td>
					<td>', $role, '</td>
				</tr>', "\n";
    }
    ?>

</table>