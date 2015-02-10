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
use Atos\Form\ConfigForm;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Exception\FileException;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

/**
 * Class ConfigureController
 * @package Atos\Controller
 * @author manuel raynaud <mraynaud@openstudio.fr>, Franck Allimant <franck@cqfdev.fr>
 */
class ConfigureController extends BaseAdminController
{
    public function displayConfigurationPage()
    {
        $logFilePath = sprintf(THELIA_ROOT."log".DS."%s.log", Atos::MODULE_DOMAIN);

        $traces = @file_get_contents($logFilePath);

        if (false === $traces) {
            $traces = $this->getTranslator()->trans(
                "The log file '%log' does not exists yet.",
                [ '%log' => $logFilePath ],
                Atos::MODULE_DOMAIN
            );
        } elseif (empty($traces)) {
            $traces = $this->getTranslator()->trans("The log file is currently empty.", [], Atos::MODULE_DOMAIN);
        }

        return $this->render(
            'module-configure',
            [
                'module_code' => 'Atos',
                'trace_content' => nl2br($traces)
            ]
        );
    }

    public function copyDistFile($fileName, $merchantId)
    {
        $distFile =  Atos::getConfigDirectory() . $fileName . '.dist';
        $destFile = Atos::getConfigDirectory() . $fileName . '.' . $merchantId;

        if (! is_readable($destFile)) {
            if (!is_file($distFile) && !is_readable($distFile)) {
                throw new FileException(sprintf("Can't read file '%s', please check file permissions", $distFile));
            }

            // Copy the dist file in place
            $fs = new Filesystem();

            $fs->copy($distFile, $destFile);
        }

        return $destFile;
    }

    public function checkExecutable($fileName)
    {
        $binFile = Atos::getBinDirectory() . DS . $fileName;

        if (! is_executable($binFile)) {
            throw new FileException(
                $this->getTranslator()->trans(
                    "The '%file' should be executable. Please check file permission",
                    [ '%file' => $binFile ],
                    Atos::MODULE_DOMAIN
                )
            );
        }
    }

    public function configure()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'atos', AccessManager::UPDATE)) {
            return $response;
        }

        $form = new ConfigForm($this->getRequest());
        $error_msg = null;

        try {
            $configForm = $this->validateForm($form);

            // Get the form field values
            $data = $configForm->getData();

            foreach ($data as $name => $value) {
                if (is_array($value)) {
                    $value = implode(';', $value);
                }

                ConfigQuery::write($name, $value, 1, 1);
            }

            $merchantId = $data['atos_merchantId'];

            $this->checkExecutable('request');
            $this->checkExecutable('response');

            $this->copyDistFile('parmcom', $merchantId);
            $certificateFile = $this->copyDistFile('certif.fr', $merchantId);

            // Write certificate
            if (! @file_put_contents($certificateFile, $data['atos_certificate'])) {
                throw new FileException(
                    $this->getTranslator()->trans(
                        "Failed to write certificate data in file '%file'. Please check file permission",
                        [ '%file' => $certificateFile ],
                        Atos::MODULE_DOMAIN
                    )
                );
            }

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
