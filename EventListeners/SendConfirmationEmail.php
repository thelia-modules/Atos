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
use Thelia\Core\Template\ParserInterface;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;

/**
 * Class SendEmailConfirmation
 * @package Atos\EventListeners
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class SendConfirmationEmail implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * @var MailerFactory
     */
    protected $mailer;

    public function __construct(ParserInterface $parser, MailerFactory $mailer)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    public function updateStatus(OrderEvent $event)
    {
        $atos = new Atos();
        $order = $event->getOrder();
        if ($order->isPaid() && $atos->isPaymentModuleFor($order)) {
            $this->mailer->sendEmailToCustomer(
                Atos::CONFIRMATION_MESSAGE_NAME,
                $order->getCustomer(),
                [
                    'order_id' => $order->getId(),
                    'order_ref' => $order->getRef()
                ]
            );

            Tlog::getInstance()->debug("Confirmation email sent to customer " . $order->getCustomer()->getEmail());
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::ORDER_UPDATE_STATUS => ["updateStatus", 128]
        ];
    }
}
