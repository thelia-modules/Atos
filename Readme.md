# Atos-SIPS Payment Module
------------------------

## English instructions

This module offers to your customers the Atos SIPS payment system, which is widely used by the french banks under different names: Mercanet, E-Transactions, Citelis, and much more.

### Installation

#### Manually

Install the Atos module using the Module page of your back office to upload the archive.

You can also extract the archive in the `<thelia root>/local/modules` directory. Be sure that the name of the module's directory is `Atos` (and not `Atos-master`, for example).

Activate the module from the Modules page of your back-office.

#### composer

```
$ composer require thelia/atos-module:~1.0
```

### Usage

You have to configure the Atos module before starting to use it. To do so, go to the "Modules" tab of your Thelia back-office, and activate the Atos module.

Then click the "Configure" button, and enter the required information. In most case, you'll receive your merchant ID by e-mail, and you'll receive instructions to download your certificate.

The module performs several checks when the configuration is saved, especially the execution permissions on the Atos binaries.

During the test phase, you can define the IP addresses allowed to use the Atos module on the front office, so that your customers will not be able to pay with Atos during this test phase. 

A log of Atos post-payment callbacks is displayed in the configuration page.

### Payment template

You can customize the payment page ```templates/atos/payment.html``` to provide a better integration in your template, but the payment form data cannot be modified, as it is generated and signed by the Atos binary.

## Instructions en français

Ce module permet à vos clients de payer leurs commande par carte bancaire via la plate-forme Atos SIPS, utilisée par de nombreuses banques françaises sous diverses dénominations commerciales: Mercanet, Citelis, E-Transactions, et bien d'autres.

## Installation

### Manuellement

Installez ce module directement depuis la page Modules de votre back-office, en envoyant le fichier zip du module.

Vous pouvez aussi décompresser le module, et le placer manuellement dans le dossier ```<thelia_root>/local/modules```. Assurez-vous que le nom du dossier est bien ```Atos```, et pas ```Atos-master```

### composer

```
$ composer require thelia/atos-module:~1.0
```


## Utilisation

Pour utiliser le module Atos, vous devez tout d'abord le configurer. Pour ce faire, rendez-vous dans votre back-office, onglet Modules, et activez le module Atos.

Cliquez ensuite sur "Configurer" sur la ligne du module, et renseignez les informations requises. Dans la plupart des cas, l'ID Marchand vous a été communiqué par votre banque par e-mail, et vous devez recevoir les instructions qui vous permettront de télécharger le certificat.

Le module réalise plusieurs vérifications de votre configuration, et vous signalera les problèmes éventuellement rencontrés. Il contrôle notamment que les exécutables Atos possèdent bien les permissions d'exécution.

Lors de la phase de test, vous pouvez définir les adresses IP qui seront autorisées à utiliser le module en front-office, afin de ne pas laisser vos clients payer leur commandes avec Atos pendant cette phase.

## Template de paiement

Vous pouvez adapter la page de paiement qui se trouve dans ```templates/atos/payment.html```, et l'adapter à votre template, mais la form de paiement en elle-même ne peut pas être modifiée, elle est générée et signée par le binaire Atos, et ne doit pas être modifiée.