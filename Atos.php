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

use Atos\Model\AtosCurrencyQuery;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Model\Config;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Tools\URL;

class Atos extends AbstractPaymentModule
{
    const MODULE_DOMAIN = 'atos';

    private $parameters;

    public function postActivation(ConnectionInterface $con = null)
    {
        if (null === ConfigQuery::read('atos_merchantId')) {
            $merchantConfig = new Config();

            $merchantConfig->setName('atos_merchantId')
                ->setHidden(true)
                ->setSecured(true)
                ->save($con);
            ;

            ConfigQuery::write('atos_transactionId', 1, 1, 1);
        }

        $database = new Database($con);

        $database->insertSql(null, array(
            __DIR__ . DS . 'Config'.DS.'thelia.sql'
        ));
    }

    /**
     * @param string $key atos key parameter
     * @param string $value parameter value
     * @return $this
     */
    private function addParam($key, $value)
    {
        $this->parameters = sprintf("%s %s=%s",$this->parameters, $key, $value);
        return $this;
    }

    private function getParameters()
    {
        return trim($this->parameters);
    }

    /**
     *
     * generate a transaction id for atos solution
     *
     * @param Order $order
     * @return int|mixed
     */
    private function generateTransactionID(Order $order)
    {
        $transId = ConfigQuery::read('atos_transactionId', 1);

        $transId = 1 + $transId;

        if (strlen($transId) > 6) {
            $transId = 1;
        }

        ConfigQuery::write('atos_transactionId', $transId, 1, 1);

        return $transId;
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
        $pathBin = __DIR__ . DS . 'bin'. DS .'request';

        $atosCurrency = AtosCurrencyQuery::create()
            ->findPk($order->getCurrency()->getCode());

        if (null == $atosCurrency) {
            throw new \InvalidArgumentException(
                sprintf("Atos does not supprot this currency : %s",
                    $order->getCurrency()->getCode()
                )
            );
        }

        $amount = $order->getTotalAmount();
        $amount = number_format($amount, $atosCurrency->getDecimals(), '', '');

        $transactionId = $this->generateTransactionID($order);

        $order->setTransactionRef($transactionId)->save();

        $router = $this->getContainer()->get('router.atos');

        $this->addParam('pathfile', __DIR__. DS . 'Config' . DS . 'pathfile')
            ->addParam('merchant_id', ConfigQuery::read('atos_merchantId'))
            ->addParam('customer_email', $order->getCustomer()->getEmail())
            ->addParam('currency_code', $atosCurrency->getAtosCode())
            ->addParam('amount', $amount)
            ->addParam('transaction_id', $transactionId)
            ->addParam('order_id', $order->getId())
            ->addParam('automatic_response_url', URL::getInstance()->absoluteUrl($router->generate('atos.payment.confirmation')))
            ->addParam('cancel_return_url', $this->getPaymentFailurePageUrl($order->getId(), Translator::getInstance()->trans('you cancel the payment', [], Atos::MODULE_DOMAIN)))
            ->addParam('normal_return_url', $this->getPaymentSuccessPageUrl($order->getId()))
        ;

        $encrypt = exec(sprintf("%s %s", $pathBin, $this->getParameters()));

        $datas = explode('!', $encrypt);

        if ($datas[1] == '' && $datas[2] == '') {
            throw new \RuntimeException(
                Translator::getInstance()->trans('Request binary not found in "%s"', $pathBin)
            );
        } elseif ($datas[1] != 0) {
            throw new \RuntimeException($datas[2]);
        } else {

            $parser = $this->getContainer()->get('thelia.parser');

            $content = $parser->renderString(
                file_get_contents(__DIR__ . DS . 'templates' . DS . 'atos' . DS . 'payment.html'),
                [
                    'site_name' => ConfigQuery::read('store_name'),
                    'form' => $datas[3]
                ]
            );

            return Response::create($content);

        }
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
