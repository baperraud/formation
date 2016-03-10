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
What happens if tomorrow i decided to edit a root ?
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
    * Éditer/supprimer les news des autres auteurs. *En cours*
    * Supprimer n'importe quel commentaire. *En cours*
  * Liens vers les pages profils :
    * Le nom de l'auteur d'une news ou d'un commentaire qui fait référence à un compte utilisateurs/admin pointera vers une page de profil d'un utilisateur qui listera les news postées par ce membre et les commentaires écrits. OK
  * Mails.
    * Lorsqu'un nouveau commentaires est posté, envoyer un mail à tous les gens qui ont commenté cette même news précédement.
