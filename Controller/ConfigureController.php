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
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Thelia;
use Thelia\Exception\FileException;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;
use Thelia\Tools\Version\Version;

/**
 * Class ConfigureController
 * @package Atos\Controller
 * @author manuel raynaud <mraynaud@openstudio.fr>, Franck Allimant <franck@cqfdev.fr>
 */
class ConfigureController extends BaseAdminController
{
    public function copyDistFile($fileName, $merchantId)
    {
        $distFile = Atos::getConfigDirectory() . $fileName . '.dist';
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
        $binFile = Atos::getBinDirectory() . $fileName;

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

        $configurationForm = $this->createForm('atos_configuration');
        $message = null;

        try {
            $form = $this->validateForm($configurationForm);

            // Get the form field values
            $data = $form->getData();

            foreach ($data as $name => $value) {
                if (is_array($value)) {
                    $value = implode(';', $value);
                }

                Atos::setConfigValue($name, $value);
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
                $url = '/admin/module/Atos';
            } else {
                // If we have to close the page, go back to the module back-office page.
                $url = '/admin/modules';
            }

            return $this->generateRedirect(URL::getInstance()->absoluteUrl($url));
        } catch (FormValidationException $ex) {
            $message = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }

        $this->setupFormErrorContext(
            $this->getTranslator()->trans("Atos configuration", [], Atos::MODULE_DOMAIN),
            $message,
            $configurationForm,
            $ex
        );

        // Before 2.2, the errored form is not stored in session
        if (Version::test(Thelia::THELIA_VERSION, '2.2', false, "<")) {
            return $this->render('module-configure', [ 'module_code' => 'Atos' ]);
        } else {
            return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/Atos'));
        }
    }
}
