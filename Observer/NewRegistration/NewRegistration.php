<?php
/**
 * Created by PhpStorm.
 * User: hoangnew
 * Date: 12/04/2016
 * Time: 13:58
 */
namespace Magenest\EmailNotifications\Observer\NewRegistration;

use Magenest\EmailNotifications\Observer\Email\Email;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;


class NewRegistration extends Email implements ObserverInterface
{


    public function execute(Observer $observer)
    {

            $receiverList = $this->_scopeConfig->getValue(
                $this->registration('rv_receive'),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $receiverEmails = json_decode($receiverList,true);
            foreach ($receiverEmails as $receiverEmail) {
                $Email = $receiverEmail['email'];
                try {
                    $template_id = $this->_scopeConfig->getValue(
                        $this->registration('rv_template'),
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    $customer_name = $observer->getEvent()->getCustomer()->getFirstname() . ' ' . $observer->getEvent()->getCustomer()->getLastname();
                    $transport = $this->_transportBuilder->setTemplateIdentifier($template_id)->setTemplateOptions(
                       $this->transport()
                    )->setTemplateVars(
                        [
                            'customerName' => $customer_name,
                            'customerEmail' => $observer->getEvent()->getCustomer()->getEmail()
                        ]
                    )->setFrom(
                        $this->Emailsender()
                    )->addTo(
                        $Email
                    )->getTransport();
                    $transport->sendMessage();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->_logger->critical($e);
                }
            }
        }
}
