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
 * Class PagSeguroPaymentParser
 */
class PagSeguroPaymentParser extends PagSeguroServiceParser
{

    /***
     * @param $payment PagSeguroPaymentRequest
     * @return mixed
     */
    public static function getData($payment)
    {
        
        $data = null;

        // pre-approval
        if (property_exists($payment, 'preApproval')) {
            if ($payment->getPreApproval() != null) {
                $data = PagSeguroPreApprovalParser::getData($payment->getPreApproval());
            }
        }
       
        // reference
        if ($payment->getReference() != null) {
            $data["reference"] = $payment->getReference();
        }

        // sender
        if ($payment->getSender() != null) {
            if ($payment->getSender()->getName() != null) {
                $data['sender.name'] = $payment->getSender()->getName();
            }
            if ($payment->getSender()->getEmail() != null) {
                $data['sender.email'] = $payment->getSender()->getEmail();
            }

            // phone
            if ($payment->getSender()->getPhone() != null) {
                if ($payment->getSender()->getPhone()->getAreaCode() != null) {
                    $data['sender.areaCode'] = $payment->getSender()->getPhone()->getAreaCode();
                }
                if ($payment->getSender()->getPhone()->getNumber() != null) {
                    $data['sender.phone'] = $payment->getSender()->getPhone()->getNumber();
                }
            }

            // documents
            /*** @var $document PagSeguroDocument */
            if ($payment->getSender()->getDocuments() != null) {
                $documents = $payment->getSender()->getDocuments();
                if (is_array($documents) && count($documents) == 1) {
                    foreach ($documents as $document) {
                        if (!is_null($document)) {
                            $document->getType() == "CPF" ? 
                                $data['sender.CPF'] = $document->getValue() : 
                                $data['sender.CNPJ'] = $document->getValue();
                        }
                    }
                }
            }

            if ($payment->getSender()->getIP() != null) {
                $data['ip'] = $payment->getSender()->getIP();
            }
        }

        // currency
        if ($payment->getCurrency() != null) {
            $data['currency'] = $payment->getCurrency();
        }

        // items
        $items = $payment->getItems();
        if (count($items) > 0) {
            $i = 0;

            foreach ($items as $key => $value) {
                $i++;
                if ($items[$key]->getId() != null) {
                    $data["item[$i].id"] = $items[$key]->getId();
                }
                if ($items[$key]->getDescription() != null) {
                    $data["item[$i].description"] = $items[$key]->getDescription();
                }
                if ($items[$key]->getQuantity() != null) {
                    $data["item[$i].quantity"] = $items[$key]->getQuantity();
                }
                if ($items[$key]->getAmount() != null) {
                    $amount = PagSeguroHelper::decimalFormat($items[$key]->getAmount());
                    $data["item[$i].amount"] = $amount;
                }
                if ($items[$key]->getWeight() != null) {
                    $data["item[$i].weight"] = $items[$key]->getWeight();
                }
                if ($items[$key]->getShippingCost() != null) {
                    $data["item[$i].shippingCost"] = PagSeguroHelper::decimalFormat($items[$key]->getShippingCost());
                }
            }
        }

        // receivers
        $receivers = $payment->getReceivers();
        if (count($receivers) > 0) {
            $i = 0;

            foreach ($receivers as $key => $value) {
                $i++;
                if ($receivers[$key]->getPublicKey() != null) {
                    $data["receiver[$i].publicKey"] = $receivers[$key]->getPublicKey();
                }
                if ($receivers[$key]->getAmount() != null) {
                    $data["receiver[$i].split.amount"] = $receivers[$key]->getAmount();
                }
            }
        }

        // extraAmount
        if ($payment->getExtraAmount() != null) {
            $data['extraAmount'] = PagSeguroHelper::decimalFormat($payment->getExtraAmount());
        }

        // shipping
        if ($payment->getShipping() != null) {
            if ($payment->getShipping()->getType() != null && $payment->getShipping()->getType()->getValue() != null) {
                $data['shipping.type'] = $payment->getShipping()->getType()->getValue();
            }

            if ($payment->getShipping()->getCost() != null && $payment->getShipping()->getCost() != null) {
                $data['shipping.cost'] = PagSeguroHelper::decimalFormat($payment->getShipping()->getCost());
            }

            // address
            if ($payment->getShipping()->getAddress() != null) {
                if ($payment->getShipping()->getAddress()->getStreet() != null) {
                    $data['shipping.address.street'] = $payment->getShipping()->getAddress()->getStreet();
                }
                if ($payment->getShipping()->getAddress()->getNumber() != null) {
                    $data['shipping.address.number'] = $payment->getShipping()->getAddress()->getNumber();
                }
                if ($payment->getShipping()->getAddress()->getComplement() != null) {
                    $data['shipping.address.complement'] = $payment->getShipping()->getAddress()->getComplement();
                }
                if ($payment->getShipping()->getAddress()->getCity() != null) {
                    $data['shipping.address.city'] = $payment->getShipping()->getAddress()->getCity();
                }
                if ($payment->getShipping()->getAddress()->getState() != null) {
                    $data['shipping.address.state'] = $payment->getShipping()->getAddress()->getState();
                }
                if ($payment->getShipping()->getAddress()->getDistrict() != null) {
                    $data['shipping.address.district'] = $payment->getShipping()->getAddress()->getDistrict();
                }
                if ($payment->getShipping()->getAddress()->getPostalCode() != null) {
                    $data['shipping.address.postalCode'] = $payment->getShipping()->getAddress()->getPostalCode();
                }
                if ($payment->getShipping()->getAddress()->getCountry() != null) {
                    $data['shipping.address.country'] = $payment->getShipping()->getAddress()->getCountry();
                }
            }
        }
        // maxAge
        if ($payment->getMaxAge() != null) {
            $data['maxAge'] = $payment->getMaxAge();
        }
        // maxUses
        if ($payment->getMaxUses() != null) {
            $data['maxUses'] = $payment->getMaxUses();
        }

        // redirectURL
        if ($payment->getRedirectURL() != null) {
            $data['redirectURL'] = $payment->getRedirectURL();
        }

        // notificationURL
        if ($payment->getNotificationURL() != null) {
            $data['notificationURL'] = $payment->getNotificationURL();
        }

        // metadata
        if (count($payment->getMetaData()->getItems()) > 0) {
            $i = 0;
            foreach ($payment->getMetaData()->getItems() as $item) {
                if ($item instanceof PagSeguroMetaDataItem) {
                    if (!PagSeguroHelper::isEmpty($item->getKey()) && !PagSeguroHelper::isEmpty($item->getValue())) {
                        $i++;
                        $data['metadataItemKey' . $i] = $item->getKey();
                        $data['metadataItemValue' . $i] = $item->getValue();

                        if (!PagSeguroHelper::isEmpty($item->getGroup())) {
                            $data['metadataItemGroup' . $i] = $item->getGroup();
                        }
                    }
                }
            }
        }

        // paymentMethodConfig
        if (count($payment->getPaymentMethodConfig()->getConfig()) > 0) {
            $i = 0;
            foreach ($payment->getPaymentMethodConfig()->getConfig() as $config) {
                if ($config instanceof PagSeguroPaymentMethodConfigItem) {
                    if (!PagSeguroHelper::isEmpty($config->getKey())
                        && !PagSeguroHelper::isEmpty($config->getValue()))
                    {
                        $i++;
                        if (!PagSeguroHelper::isEmpty($config->getGroup())) {
                            $data['paymentMethodGroup' . $i] = $config->getGroup();
                        }
                        $data['paymentMethodConfigKey' . $i . "_1"] = $config->getKey();
                        $data['paymentMethodConfigValue' . $i . "_1"] = $config->getValue();
                    }
                }
            }
        }

        // AcceptedPaymentMethod
        if (count($payment->getAcceptedPaymentMethod()->getConfig()) > 0) {
            $i = 0;
            foreach ($payment->getAcceptedPaymentMethod()->getConfig() as $acceptedPayment) {
                if ($acceptedPayment instanceof PagSeguroAcceptPaymentMethod) {
                    $data['acceptPaymentMethodGroup'] = $acceptedPayment->getGroup();
                    $data['acceptPaymentMethodName'] = $acceptedPayment->getName();
                }
                if ($acceptedPayment instanceof PagSeguroExcludePaymentMethod) {
                    $data['excludePaymentMethodGroup'] = $acceptedPayment->getGroup();
                    $data['excludePaymentMethodName'] = $acceptedPayment->getName();
                }
            }
        }

        // parameter
        if (count($payment->getParameter()->getItems()) > 0) {
            foreach ($payment->getParameter()->getItems() as $item) {
                if ($item instanceof PagSeguroParameterItem) {
                    if (!PagSeguroHelper::isEmpty($item->getKey()) && !PagSeguroHelper::isEmpty($item->getValue())) {
                        if (!PagSeguroHelper::isEmpty($item->getGroup())) {
                            $data[$item->getKey() . '' . $item->getGroup()] = $item->getValue();
                        } else {
                            $data[$item->getKey()] = $item->getValue();
                        }
                    }
                }
            }
        }

        return $data;
    }

    /***
     * @param $str_xml
     * @return PagSeguroPaymentParserData Success
     */
    public static function readSuccessXml($str_xml)
    {
        $parser = new PagSeguroXmlParser($str_xml);
        $data = $parser->getResult('checkout');
        $PaymentParserData = new PagSeguroParserData();
        $PaymentParserData->setCode($data['code']);
        $PaymentParserData->setRegistrationDate($data['date']);
        return $PaymentParserData;
    }

    /***
     * @param $str_xml
     * @return parsed transaction
     */
    public static function readTransactionXml($str_xml)
    {
        $parser = new PagSeguroXmlParser($str_xml);
        $data = $parser->getResult('transaction');
        $PaymentParserData = new PagSeguroParserData();
        $PaymentParserData->setCode($data['code']);
        $PaymentParserData->setRegistrationDate($data['date']);
        return $PaymentParserData;
    }
}
