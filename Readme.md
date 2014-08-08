Atos
----

This module offers to your customers the Atos sips payment system.

## Installation

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Atos.
* Activate it in you thelia administration panel, Then click on "configure" and enter the merchant_id provided by you bank.
* rename files ```Config/certif.fr.xxxxxxxxxxxxxxxx``` to ```Config/certif.fr.<merchant_id>``` and ```parmcom.xxxxxxxxxxxxxxxx``` to ```parmcom.<merchant_id>```.
For example if your merchant_id is ```011223344551111``` you have rename the file with this merchant_id : ```Config/certif.fr.011223344551111``` and ```parmcom.011223344551111```
* Ensure that the binaries file in bin directory can be execute by the server.

## Usage

After installing the module you have nothing to do, just test it before switching your contrat to production mode.

## Payment template

Atos binary generate all the form for you so it's not possible to customize it simply. By the way you can customize the template present in ```templates/atos/payment.html``` but you can't customize the form, you can't inherit
from your default layout.