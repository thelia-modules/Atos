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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Module\BasePaymentModuleController;


/**
 * Class PaymentController
 * @package Atos\Controller
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class PaymentController extends BasePaymentModuleController
{

    public function displayLogo($image)
    {
        if(file_exists(__DIR__ . DS . '..' . DS . 'logo' . DS . $image)) {
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