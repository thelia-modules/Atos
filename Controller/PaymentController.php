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
use Thelia\Core\HttpFoundation\Response;
use Thelia\Model\OrderQuery;
use Thelia\Module\BasePaymentModuleController;

/**
 * Class PaymentController
 * @package Atos\Controller
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class PaymentController extends BasePaymentModuleController
{

    public function processAtosRequest()
    {
        $binResponse = Atos::getBinDirectory() .DS . 'response';

        $data = escapeshellcmd($_POST['DATA']);

        $pathfile = Atos::getPathfilePath();

        $resultRaw = exec(sprintf("%s message=%s pathfile=%s", $binResponse, $data, $pathfile));

        $result = explode('!', $resultRaw);

        $result = $this->parseResult($result);

        if ($result['response_code'] == '00') {
            $atos = new Atos();
            $order = OrderQuery::create()
                ->filterByTransactionRef($result['transaction_id'])
                ->filterByPaymentModuleId($atos->getModuleModel()->getId())
                ->findOne();

            if ($order) {
                $this->confirmPayment($order->getId());
            }
        }

        if ($result['code'] == '' && $result['error'] == '') {
            $this->getLog()
                ->addError(sprintf('Response request not found in %s'. $binResponse));

        } elseif ($result['error'] != 0) {
            $this->getLog()
                ->addError(sprintf('error during response process with message : %s', $result['error']));
        }

        $this->getLog()
            ->addInfo(sprintf('response parameters : %s', print_r($result, true)));

        return Response::create();

    }

    protected function parseResult($result) {
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
            'return_context' => $result[20],
            'caddie' => $result[21],
            'receipt_complement' => $result[22],
            'merchant_language' => $result[23],
            'language' => $result[24],
            'customer_id' => $result[25],
            'order_id' => $result[26],
            'customer_email' => $result[27],
            'customer_ip_address' => $result[28],
            'capture_day' => $result[29],
            'capture_mode' => $result[30],
            'data' => $result[31]
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
