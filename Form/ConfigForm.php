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
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 * Class Config
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class ConfigForm extends BaseForm
{
    protected function buildForm()
    {
        $translator = Translator::getInstance();

        $this->formBuilder
            ->add(
                'atos_merchantId',
                'text',
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'label' => $translator->trans('Shop Merchant ID', [], Atos::MODULE_DOMAIN),
                    'label_attr' => [
                        'for' => 'merchant_id',
                    ]
                ]
            )
            ->add(
                'atos_mode',
                'choice',
                [
                    'constraints' =>  [
                        new NotBlank()
                    ],
                    'choices' => [
                        'TEST' => $translator->trans('Test', [], Atos::MODULE_DOMAIN),
                        'PRODUCTION' => $translator->trans('Production', [], Atos::MODULE_DOMAIN),
                    ],
                    'label' => $translator->trans('Operation Mode', [], Atos::MODULE_DOMAIN),
                    'label_attr' => [
                        'for' => 'mode',
                        'help' => $translator->trans('Test or production mode', [], Atos::MODULE_DOMAIN)
                    ]
                ]
            )
            ->add(
                'atos_allowed_ip_list',
                'textarea',
                [
                    'required' => false,
                    'label' => $translator->trans('Allowed IPs in test mode', [], Atos::MODULE_DOMAIN),
                    'label_attr' => [
                        'for' => 'platform_url',
                        'help' => $translator->trans(
                            'List of IP addresses allowed to use this payment on the front-office when in test mode (your current IP is %ip). One address per line',
                            [ '%ip' => $this->getRequest()->getClientIp() ],
                            Atos::MODULE_DOMAIN
                        ),
                        'rows' => 3
                    ]
                ]
            )
            ->add(
                'atos_minimum_amount',
                'integer',
                [
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThanOrEqual(['value' => 0 ])
                    ],
                    'label' => $translator->trans('Minimum order total', [], Atos::MODULE_DOMAIN),
                    'label_attr' => [
                        'for' => 'minimum_amount',
                        'help' => $translator->trans(
                            'Minimum order total in the default currency for which this payment method is available. Enter 0 for no minimum',
                            [],
                            Atos::MODULE_DOMAIN
                        )
                    ]
                ]
            )
            ->add(
                'atos_maximum_amount',
                'integer',
                [
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThanOrEqual([ 'value' => 0 ])
                    ],
                    'label' => $translator->trans('Maximum order total', [], Atos::MODULE_DOMAIN),
                    'label_attr' => [
                        'for' => 'maximum_amount',
                        'help' => $translator->trans(
                            'Maximum order total in the default currency for which this payment method is available. Enter 0 for no maximum',
                            [],
                            Atos::MODULE_DOMAIN
                        )
                    ]
                ]
            )
            ->add(
                'atos_certificate',
                'textarea',
                [
                    'required' => false,
                    'label' => $translator->trans('ATOS certificate content', [], Atos::MODULE_DOMAIN),
                    'label_attr' => [
                        'for' => 'platform_url',
                        'help' => $translator->trans(
                            'Please paste here the certificate downloaded from the Atos SIPS platform',
                            [],
                            Atos::MODULE_DOMAIN
                        ),
                        'rows' => 4
                    ]
                ]
            )
            ->add(
                'send_confirmation_message_only_if_paid',
                'checkbox',
                [
                    'value' => 1,
                    'required' => false,
                    'label' => $this->translator->trans('Send order confirmation on payment success', [], Atos::MODULE_DOMAIN),
                    'label_attr' => [
                        'help' => $this->translator->trans(
                            'If checked, the order confirmation message is sent to the customer only when the payment is successful. The order notification is always sent to the shop administrator',
                            [],
                            Atos::MODULE_DOMAIN
                        )
                    ]
                ]
            )
            ->add(
                'send_payment_confirmation_message',
                'checkbox',
                [
                    'value' => 1,
                    'required' => false,
                    'label' => $this->translator->trans('Send a payment confirmation e-mail', [], Atos::MODULE_DOMAIN),
                    'label_attr' => [
                        'help' => $this->translator->trans(
                            'If checked, a payment confirmation e-mail is sent to the customer.',
                            [],
                            Atos::MODULE_DOMAIN
                        )
                    ]
                ]
            )

        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'atos_config';
    }
}
