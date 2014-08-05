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

namespace Atos\Form;

use Atos\Atos;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ConfigQuery;

/**
 * Class Config
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class ConfigForm extends BaseForm
{

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $translator = Translator::getInstance();
        $this->formBuilder
            ->add('merchant_id', 'text', [
                'constraints' => [
                    new NotBlank(),
                ],
                'data' => ConfigQuery::read('atos_merchantId', '12345678'),
                'label' => $translator->trans('merchant id', [], Atos::MODULE_DOMAIN),
                'label_attr' => [
                    'for' => 'merchant_id',
                    'help' => $translator->trans('merchant id provided by your bank', [], Atos::MODULE_DOMAIN)
                ]
            ]);
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'atos_config';
    }
}