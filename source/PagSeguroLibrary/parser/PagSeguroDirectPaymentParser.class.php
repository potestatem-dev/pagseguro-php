<?php
/**
 * 2007-2014 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 *Licensed under the Apache License, Version 2.0 (the "License");
 *you may not use this file except in compliance with the License.
 *You may obtain a copy of the License at
 *
 *http://www.apache.org/licenses/LICENSE-2.0
 *
 *Unless required by applicable law or agreed to in writing, software
 *distributed under the License is distributed on an "AS IS" BASIS,
 *WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *See the License for the specific language governing permissions and
 *limitations under the License.
 *
 *  @author    PagSeguro Internet Ltda.
 *  @copyright 2007-2014 PagSeguro Internet Ltda.
 *  @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/***
 * Class PagSeguroDirectPaymentParser
 */
class PagSeguroDirectPaymentParser extends PagSeguroPaymentParser
{

    /***
     * @param $payment PagSeguroDirectPaymentRequest
     * @return mixed
     */
    public static function getData($payment)
    {

        $data = null;

        $data = parent::getData($payment);

        // paymentMode
        if ($payment->getPaymentMode() != null) {
            $data["payment.mode"] = $payment->getPaymentMode()->getValue();
        }

        // paymentMethod
        if ($payment->getPaymentMethod()->getPaymentMethod() != null) {
            $data["payment.method"] = $payment->getPaymentMethod()->getPaymentMethod();
        }

        // senderHash
        if ($payment->getSenderHash() != null) {
            $data["sender.hash"] = $payment->getSenderHash();
        }

         // receiverEmail
        if ($payment->getReceiverEmail() != null) {
            $data["receiverEmail"] = $payment->getReceiverEmail();
        }

         // primaryReceiver
        if ($payment->getPrimaryReceiver() != null) {
            $data["primaryReceiver.publicKey"] = $payment->getPrimaryReceiver();
        }

        // Bank name
        if ($payment->getOnlineDebit() != null) {
            $data["bank.name"] = $payment->getOnlineDebit()->getBankName();
        }

        //Credit Card
        if ($payment->getCreditCard() != null) {
            //Token
            if ($payment->getCreditCard()->getToken() != null) {
                $data['creditCard.token'] = $payment->getCreditCard()->getToken();
            }

            //Installments
            if ($payment->getCreditCard()->getInstallment() != null) {
                $installment = $payment->getCreditCard()->getInstallment();
                if ($installment->getQuantity() != null && $installment->getValue()) {
                    $data['installment.quantity'] = $installment->getQuantity();
                    $data['installment.value']    = PagSeguroHelper::decimalFormat($installment->getValue());
                    if ($installment->getNoInterestInstallmentQuantity() != null ) {
                        $data['installment.noInterestInstallmentQuantity'] = $installment->getNoInterestInstallmentQuantity();
                    }
                }
            }

            //Holder
            if ($payment->getCreditCard()->getHolder() != null) {
                $holder = $payment->getCreditCard()->getHolder();
                if ($holder->getName() != null) {
                    $data['creditCard.holder.name'] = $holder->getName();
                }
                 // documents
                /*** @var $document PagSeguroDocument */
                if ($payment->getCreditCard()->getHolder()->getDocuments() != null) {
                    $documents = $payment->getCreditCard()->getHolder()->getDocuments();
                        $data['creditCard.holder.CPF'] = $documents->getValue();
                }
                if ($holder->getBirthDate() != null) {
                    $data['creditCard.holder.birthDate'] = $holder->getBirthDate();
                }
                // phone
                if ($holder->getPhone() != null) {
                    if ($holder->getPhone()->getAreaCode() != null) {
                        $data['creditCard.holder.areaCode'] = $holder->getPhone()->getAreaCode();
                    }
                    if ($holder->getPhone()->getNumber() != null) {
                        $data['creditCard.holder.phone'] = $holder->getPhone()->getNumber();
                    }
                }
            }

            //Billing Address
            if ($payment->getCreditCard()->getBilling() != null) {
                $billingAddress = $payment->getCreditCard()->getBilling()->getAddress();
                if ($billingAddress->getStreet() != null) {
                    $data['billingAddress.street'] = $billingAddress->getStreet();
                }
                if ($billingAddress->getNumber() != null) {
                    $data['billingAddress.number'] = $billingAddress->getNumber();
                }
                if ($billingAddress->getComplement() != null) {
                    $data['billingAddress.complement'] = $billingAddress->getComplement();
                }
                if ($billingAddress->getCity() != null) {
                    $data['billingAddress.city'] = $billingAddress->getCity();
                }
                if ($billingAddress->getState() != null) {
                    $data['billingAddress.state'] = $billingAddress->getState();
                }
                if ($billingAddress->getDistrict() != null) {
                    $data['billingAddress.district'] = $billingAddress->getDistrict();
                }
                if ($billingAddress->getPostalCode() != null) {
                    $data['billingAddress.postalCode'] = $billingAddress->getPostalCode();
                }
                if ($billingAddress->getCountry() != null) {
                    $data['billingAddress.country'] = $billingAddress->getCountry();
                }
            }

        }

        return $data;
    }
}
