<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Atos;

use Thelia\Model\Config;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;
use Propel\Runtime\Connection\ConnectionInterface;

class Atos extends AbstractPaymentModule
{
    const MODULE_DOMAIN = 'atos';

    public function postActivation(ConnectionInterface $con = null)
    {
        if (null === ConfigQuery::read('atos_merchantId')) {
            $merchantConfig = new Config();

            $merchantConfig->setName('atos_merchantId')
                ->setHidden(true)
                ->setSecured(true)
                ->save($con);
            ;
        }
    }

    /**
     *
     *  Method used by payment gateway.
     *
     *  If this method return a \Thelia\Core\HttpFoundation\Response instance, this response is send to the
     *  browser.
     *
     *  In many cases, it's necessary to send a form to the payment gateway. On your response you can return this form already
     *  completed, ready to be sent
     *
     * @param  \Thelia\Model\Order $order processed order
     * @return null|\Thelia\Core\HttpFoundation\Response
     */
    public function pay(Order $order)
    {
        // TODO: Implement pay() method.
    }

    /**
     *
     * This method is call on Payment loop.
     *
     * If you return true, the payment method will de display
     * If you return false, the payment method will not be display
     *
     * @return boolean
     */
    public function isValidPayment()
    {
        return true;
    }
}
