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

namespace Atos\Controller;

use Atos\Atos;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Exception\TheliaProcessException;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderStatusQuery;
use Thelia\Module\BasePaymentModuleController;

/**
 * Class PaymentController
 * @package Atos\Controller
 * @author manuel raynaud <mraynaud@openstudio.fr>, Franck Allimant <franck@cqfdev.fr>
 */
class PaymentController extends BasePaymentModuleController
{

    public function processAtosRequest()
    {
        $this->getLog()->addInfo(
            $this->getTranslator()->trans(
                "Atos-SIPS platform request received.",
                [],
                Atos::MODULE_DOMAIN
            )
        );

        $binResponse = Atos::getBinDirectory() . 'response';

        if (! empty($_POST['DATA'])) {
            $data = escapeshellcmd($_POST['DATA']);

            $pathfile = Atos::getPathfilePath();

            $resultRaw = exec(sprintf("%s message=%s pathfile=%s", $binResponse, $data, $pathfile));

            if (!empty($resultRaw)) {
                $result = explode('!', $resultRaw);

                $result = $this->parseResult($result);

                $this->getLog()->addInfo(
                    $this->getTranslator()->trans(
                        'Response parameters : %resp',
                        ['%resp' => print_r($result, true)],
                        Atos::MODULE_DOMAIN
                    )
                );

                if ($result['code'] == '' && $result['error'] == '') {
                    $this->getLog()->addError(
                        $this->getTranslator()->trans(
                            'Response request not found in %response',
                            ['%response' => $binResponse],
                            Atos::MODULE_DOMAIN
                        )
                    );
                } elseif (intval($result['code']) != 0) {
                    $this->getLog()->addError(
                        $this->getTranslator()->trans(
                            'Error %code while processing response, with message %message',
                            ['%code' => intval($result['code']), '%message' => $result['error']],
                            Atos::MODULE_DOMAIN
                        )
                    );
                } elseif ($result['response_code'] == '00') {
                    $orderRef = base64_decode($result['return_context']);
                    $order = OrderQuery::create()->findOneByRef($orderRef);

                    if ($order) {
                        $this->confirmPayment($order->getId());

                        $this->getLog()->addInfo(
                            $this->getTranslator()->trans(
                                "Order ID %id is confirmed.",
                                ['%id' => $order->getId()],
                                Atos::MODULE_DOMAIN
                            )
                        );
                    } else {
                        $this->getLog()->addError(
                            $this->getTranslator()->trans(
                                'Cannot find an order having reference "%rc"',
                                ['%rc' => $orderRef],
                                Atos::MODULE_DOMAIN
                            )
                        );
                    }
                } else {
                    $this->getLog()->addError(
                        $this->getTranslator()->trans(
                            'Cannot validate order. Response code is %resp',
                            ['%resp' => $result['response_code']],
                            Atos::MODULE_DOMAIN
                        )
                    );
                }
            } else {
                $this->getLog()->addError(
                    $this->getTranslator()->trans(
                        'Got empty response from executable %binary, check path and permissions',
                        ['%binary' => $binResponse],
                        Atos::MODULE_DOMAIN
                    )
                );
            }
        } else {
            $this->getLog()->addError(
                $this->getTranslator()->trans(
                    'Request does not contains any data',
                    [],
                    Atos::MODULE_DOMAIN
                )
            );
        }

        $this->getLog()->info(
            $this->getTranslator()->trans(
                "Atos platform request processing terminated.",
                [],
                Atos::MODULE_DOMAIN
            )
        );

        return Response::create();
    }

    /*
     * @param $orderId int the order ID
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function processUserCancel($orderId)
    {
        $this->getLog()->addInfo(
            $this->getTranslator()->trans(
                'User canceled payment of order %id',
                ['%id' => $orderId],
                Atos::MODULE_DOMAIN
            )
        );

        try {
            if (null !== $order = OrderQuery::create()->findPk($orderId)) {
                $currentCustomerId = $this->getSecurityContext()->getCustomerUser()->getId();
                $orderCustomerId = $order->getCustomerId();

                if ($orderCustomerId != $currentCustomerId) {
                    throw new TheliaProcessException(
                        sprintf(
                            "User ID %d is trying to cancel order ID %d ordered by user ID %d",
                            $currentCustomerId,
                            $orderId,
                            $orderCustomerId
                        )
                    );
                }

                $event = new OrderEvent($order);
                $event->setStatus(OrderStatusQuery::getCancelledStatus()->getId());
                $this->dispatch(TheliaEvents::ORDER_UPDATE_STATUS, $event);
            }
        } catch (\Exception $ex) {
            $this->getLog()->addError("Error occurred while canceling order ID $orderId: " . $ex->getMessage());
        }

        $this->redirectToFailurePage(
            $orderId,
            $this->getTranslator()->trans('you cancel the payment', [], Atos::MODULE_DOMAIN)
        );
    }

    protected function parseResult($result)
    {
        return [
            'code' => $result[1],
            'error' => $result[2],
            'merchant_id' => $result[3],
            'merchant_country' => $result[4],
            'amount' => $result[5],
            'transaction_id' => $result[6],
            'payment_means' => $result[7],
            'transmission_date' => $result[8],
            'payment_time' => $result[9],
            'payment_date' => $result[10],
            'response_code' => $result[11],
            'payment_certificate' => $result[12],
            'authorisation_id' => $result[13],
            'currency_code' => $result[14],
            'card_number' => $result[15],
            'cvv_flag' => $result[16],
            'cvv_response_code' => $result[17],
            'bank_response_code' => $result[18],
            'complementary_code' => $result[19],
            'complementary_info' => $result[20],
            'return_context' => $result[21],
            'caddie' => $result[22],
            'receipt_complement' => $result[23],
            'merchant_language' => $result[24],
            'language' => $result[25],
            'customer_id' => $result[26],
            'order_id' => $result[27],
            'customer_email' => $result[28],
            'customer_ip_address' => $result[29],
            'capture_day' => $result[30],
            'capture_mode' => $result[31],
            'data' => $result[32]
        ];
    }

    public function displayLogo($image)
    {
        if (file_exists(__DIR__ . DS . '..' . DS . 'logo' . DS . $image)) {
            $sourceImage = file_get_contents(__DIR__ . DS . '..' . DS . 'logo' . DS . $image);

            return Response::create($sourceImage, 200, [
                'Content-Type' => 'image/gif',
                'Content-Length' => strlen($sourceImage)
            ]);
        } else {
            throw new NotFoundHttpException();
        }
    }
    /**
     * Return a module identifier used to calculate the name of the log file,
     * and in the log messages.
     *
     * @return string the module code
     */
    protected function getModuleCode()
    {
        return 'Atos';
    }
}
