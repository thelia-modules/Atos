Atos
----

##en_EN

This module offers to your customers the Atos sips payment system.

## Installation

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Atos.
* Activate it in your thelia administration panel, Then click on "configure" and enter the ```merchant_id``` provided by your bank.
* rename files ```Config/certif.fr.xxxxxxxxxxxxxxxx``` to ```Config/certif.fr.<merchant_id>``` and ```parmcom.xxxxxxxxxxxxxxxx``` to ```parmcom.<merchant_id>```.
For example if your merchant_id is ```011223344551111``` you have rename the file with this merchant_id : ```Config/certif.fr.011223344551111``` and ```parmcom.011223344551111```
* put the certificate provided by your bank in ```Config/certif.fr.<merchant_id>``` file.
* Ensure that the binaries file in bin directory can be executed by the server.

## Usage

After installing the module you have nothing to do, just test it before switching your contract to production mode.

## Payment template

Atos binary generate all the form for you so it's not possible to customize it simply. By the way you can customize the template present in ```templates/atos/payment.html``` but you can't customize the form, you can't inherit
from your default layout.


##fr_FR

Module de paiement sécurisé par carte bancaire.

## Installation

Copiez le module dans le répertoire <thelia_root>/local/modules/.
Vérifiez bien que le nom du module soit Atos.
Activez le module dans l'interface d'administration. 
Ensuite cliquez sur "configurez" et entrez l'id marchand envoyé par votre banque. 
Renommez les fichiers :
*- Config/certif.fr.xxxxxxxxxxxxxxxx  par Config/certif.fr.<merchant_id> 
*- parmcom.xxxxxxxxxxxxxxxx par parmcom.<merchant_id>.
Par exemple, si votre 'merchant_id' is 011223344551111 vous devez renommez les fichiers avec ce 'merchant_id' : Config/certif.fr.011223344551111 et parmcom.011223344551111

Déposez le certificat fourni par votre banque dans le dossier Config/certif.fr.<merchant_id>.
Vérifiez bien que les binaires sont exécutables sur votre serveur.

## Utilisation 

Une fois le module installé, vous n'avez plus rien à faire. 
Simplement testez le module avant de passer votre contrat en mode "production". 


## Template de paiement

Les binaires Atos génèrent tous les formulaires. Ces formulaires ne peuvent donc pas être personnalisés.

Cependant vous pouvez personnaliser le template present dans ```templates/atos/payment.html```.

 
