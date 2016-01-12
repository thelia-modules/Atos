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

namespace Atos\EventListeners;

use Atos\Atos;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;

/**
 * Class SendEmailConfirmation
 * @package Atos\EventListeners
 * @author manuel raynaud <mraynaud@openstudio.fr>
 * @author franck allimant <franck@cqfdev.fr>
 */
class SendConfirmationEmail implements EventSubscriberInterface
{
    /**
     * @var MailerFactory
     */
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param OrderEvent $event
     *
     * @throws \Exception if the message cannot be loaded.
     */
    public function sendConfirmationEmail(OrderEvent $event)
    {
        if (Atos::getConfigValue('send_confirmation_message_only_if_paid')) {
            // We send the order confirmation email only if the order is paid
            $order = $event->getOrder();

            if (! $order->isPaid() && $order->getPaymentModuleId() == Atos::getModuleId()) {
                $event->stopPropagation();
            }
        }
    }

    /*
     * @params OrderEvent $order
     * Checks if order payment module is paypal and if order new status is paid, send an email to the customer.
     */

    public function updateStatus(OrderEvent $event)
    {
        $order = $event->getOrder();

        if ($order->isPaid() && $order->getPaymentModuleId() == Atos::getModuleId()) {
            if (Atos::getConfigValue('send_payment_confirmation_message')) {
                $this->mailer->sendEmailToCustomer(
                    Atos::CONFIRMATION_MESSAGE_NAME,
                    $order->getCustomer(),
                    [
                        'order_id'  => $order->getId(),
                        'order_ref' => $order->getRef()
                    ]
                );
            }

            // Send confirmation email if required.
            if (Atos::getConfigValue('send_confirmation_message_only_if_paid')) {
                $event->getDispatcher()->dispatch(TheliaEvents::ORDER_SEND_CONFIRMATION_EMAIL, $event);
            }

            Tlog::getInstance()->debug("Confirmation email sent to customer " . $order->getCustomer()->getEmail());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS           => array("updateStatus", 128),
            TheliaEvents::ORDER_SEND_CONFIRMATION_EMAIL => array("sendConfirmationEmail", 129)
        );
    }
}
