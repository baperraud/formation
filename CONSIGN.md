## Exercices for Baptiste

# 0 - Build the full FrameWork from OCRoom : Done !

# 1 - Add Feature : Allow logged user to logout :
* Add a link in menu to logout the connected user. 
* Make a new action in ConnexionController.
* After logout, redirect to homepage. 

# 2 - Refactoring user managment :
* Store user in Database : Create table(s) according to DreamCentury database nomenclature.
* Add a new Manager to work the new table(s) (according to the Framework manager name nomenclature).
* Update your ConnexionController code.

# 3 - CSS : Fix the CSS error when text is too long.
* Try to enter a very long text in title and text of a News and observe the result. (Long text without space)
* Fix visual problem by adding somes CSS rules.

# 4 - Fix SQL Injection : 
* If you have good memory, you know that SQL Injection mean ! 
* Remove all SQL Injection vulnerabilities. 

# 5 - Fix JavaScript Injection :
* Google is your best friend.
* Remove all JavaScript Injection vulnerabilities. 

# 6 - Improve your code : Url and Link
Actually, you need to enter manually the value of a href attribute according to the route.xml file. 
What happens if tomorrow i decided to edit a route ?
All your code is break down.
This part consist to add a functionnality for ask a route for a Controller and an Action using a function. Replace the manually entered href by a call of this function.

# 2 (suite) - Refactoring user managment :
* Modifications supplémentaires :
  * Les utilisateurs sont maintenant des auteurs
  * Un visiteur peut donc :
    * S'inscrire en tant qu'Auteur (pseudo, email et password). OK
    * Poster un commentaire en saisissant sont pseudo (et son email : nullable) OK
  * Un utilisateur connecté peut donc :
    * Créer une news. OK
    * Éditer/supprimer ses news. OK
    * Poster un commentaire qui sera automatiquement associé à son compte utilisateur (il ne rentrera donc ni pseudo ni email). OK
  * Un administrateur connecté peut donc :
    * Faire tout ce que peut faire un utilisateurs connecté ( il y a donc héritage de droits). OK
    * Éditer/supprimer les news des autres auteurs. OK
    * Supprimer n'importe quel commentaire. OK
  * Liens vers les pages profils :
    * Le nom de l'auteur d'une news ou d'un commentaire qui fait référence à un compte utilisateurs/admin pointera vers une page de profil d'un utilisateur qui listera les news postées par ce membre et les commentaires écrits. OK
  * Mails.
    * Lorsqu'un nouveau commentaires est posté, envoyer un mail à tous les gens qui ont commenté cette même news précédement. OK

# 7 - Centralisation - gestion des composants génériques du site
* Mettre en place une centralisation pour la gestion des composants génériques du site.
* Actuellement, le menu est géré dans le layout. On pourrait le faire évoluer, avec des parties à afficher en fonction du membre connecté. Il faudrait contrôler le menu par le code.
* Idem pour la gestion de cookies pour la reconnexion, pour la redirection automatique en cas de non connexion lors de certaines actions, etc.
* En fait, il faudrait gérer la centralisation des actions des contrôleurs dans un nouvel élément du framework
* Il faut créer un nouveau composant (ne pas le mettre dans Application ou autre)

# 8 - Add Feature : Ajax ! Flower Party :)
* Change the behaviour of the form to add a comment in a news page.
* Form to add new comment must work now with ajax. So when user submit his form, don't reload the page but post an ajax query to valid the form.
* Show errors or add new comment directly if there are no error.
* The returned data of any ajax call need to be a JSON Object.


* Corrections :
1. Pas de génération du form via AJAX
2. Utiliser localement les triggers/listeners
3. Externaliser le script
4. Modifier les routes pour attribut format
5. Ajouter les actions de ce format dans les classes spécifiques
6. Modifier les contrôleurs pour avoir une action différente lorsque le format est différent
7. Modifier la génération du retour (pas ob_flush, json_encode)
8. Deux ids pour les deux forms (ou utiliser this)
9. Gérer la récupération de tous les commentaires depuis le dernier affiché lorsqu'on en poste un nouveau
10. Pour les kékés qui ont du temps libre : sync les formulaires

