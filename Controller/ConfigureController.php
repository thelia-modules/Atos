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

use Atos\Form\ConfigForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

/**
 * Class ConfigureController
 * @package Atos\Controller
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class ConfigureController extends BaseAdminController
{

    public function configure()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'atos', AccessManager::UPDATE)) {
            return $response;
        }

        $form = new ConfigForm($this->getRequest());
        $error_msg = null;
        try {

            $configForm = $this->validateForm($form);

            ConfigQuery::write('atos_merchantId', $configForm->get('merchant_id')->getData(), 1, 1);

            // Log configuration modification
            $this->adminLogAppend(
                "atos.configuration.message",
                AccessManager::UPDATE,
                "Atos configuration updated"
            );

            // Redirect to the success URL,
            if ($this->getRequest()->get('save_mode') == 'stay') {
                // If we have to stay on the same page, redisplay the configuration page/
                $route = '/admin/module/Atos';
            } else {
                // If we have to close the page, go back to the module back-office page.
                $route = '/admin/modules';
            }

            $response = RedirectResponse::create(URL::getInstance()->absoluteUrl($route));

        } catch (FormValidationException $e) {
            $error_msg = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $error_msg = $e->getMessage();
        }

        if (null !== $error_msg) {
            $this->setupFormErrorContext(
                'Atos Configuration',
                $error_msg,
                $form,
                $e
            );

            $response = $this->render(
                'module-configure',
                ['module_code' => 'Atos']
            );
        }

        return $response;
    }
}
