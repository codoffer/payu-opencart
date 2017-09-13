<?php

class ControllerExtensionPaymentPayu extends Controller {

    public function index() {
        $data['button_confirm'] = $this->language->get('button_confirm');
        $this->load->model('checkout/order');
        $this->language->load('extension/payment/payu');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['merchant'] = $this->config->get('payment_payu_merchant');

        $currency_code = $order_info['currency_code'];

        if ($currency_code != 'INR') {

            $getAmount = file_get_contents("http://www.google.com/finance/converter?a=" . (int) $order_info['total'] . "&from=" . $currency_code . "&to=INR");

            $getAmount = explode("<span class=bld>", $getAmount);
            $getAmount = explode("</span>", $getAmount[1]);
            $convertedAmount = preg_replace("/[^0-9\.]/", null, $getAmount[0]);
            $calculatedAmount_INR = round($convertedAmount, 2);
        } else {

            $calculatedAmount_INR = $order_info['total'];
        }



        /////////////////////////////////////Start Payu Vital  Information /////////////////////////////////

        if ($this->config->get('payment_payu_test') == 'demo') {
            $data['action'] = 'https://test.payu.in/_payment';
        } else {
            $data['action'] = 'https://secure.payu.in/_payment';
        }

        $txnid = $this->session->data['order_id'] + 9909110;


        $data['key'] = $this->config->get('payment_payu_merchant');
        $data['salt'] = $this->config->get('payment_payu_salt');
        $data['txnid'] = $txnid;
        $data['amount'] = $calculatedAmount_INR;
        $data['productinfo'] = 'opencart products information';
        $data['firstname'] = $order_info['payment_firstname'];
        $data['lastname'] = $order_info['payment_lastname'];
        $data['zipcode'] = $order_info['payment_postcode'];
        $data['email'] = $order_info['email'];
        $data['phone'] = $order_info['telephone'];
        $data['address1'] = $order_info['payment_address_1'];
        $data['address2'] = $order_info['payment_address_2'];
        $data['state'] = $order_info['payment_zone'];
        $data['city'] = $order_info['payment_city'];
        $data['country'] = $order_info['payment_country'];
        $data['pg'] = $this->config->get('payment_payu_payment_gateway');
        $data['bankcode'] = $this->config->get('payment_payu_bankcode_val');
		
        $data['surl'] = $this->url->link('extension/payment/payu/callback'); //HTTP_SERVER.'/index.php?route=payment/payu/callback';
        $data['Furl'] = $this->url->link('extension/payment/payu/callback'); //HTTP_SERVER.'/index.php?route=payment/payu/callback';
        $data['curl'] = $this->url->link('extension/payment/payu/callback');
        $key = $this->config->get('payment_payu_merchant');
		
        $productInfo = $data['productinfo'];
        $firstname = $order_info['payment_firstname'];
        $email = $order_info['email'];
        $salt = $this->config->get('payment_payu_salt');
		
        $Hash = hash('sha512', $key . '|' . $txnid . '|' . $calculatedAmount_INR . '|' . $productInfo . '|' . $firstname . '|' . $email . '|||||||||||' . $salt);
		
        $data['user_credentials'] = $this->data['key'] . ':' . $this->data['email'];
        $data['Hash'] = $Hash;
        $data['ismobileview'] = self::is_mobile();
        /////////////////////////////////////End Payu Vital  Information /////////////////////////////////
        return $this->load->view('extension/payment/payu', $data);
    }
	
    public function callback() {
	
        if (isset($this->request->post['key']) && ($this->request->post['key'] == $this->config->get('payment_payu_merchant'))) {
            $this->language->load('extension/payment/payu');
		
            $data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
			
            if (!isset($this->request->server['HTTPS']) || ($this->request->server['HTTPS'] != 'on')) {
                $data['base'] = HTTP_SERVER;
            } else {
                $data['base'] = HTTPS_SERVER;
            }
            $data['custom_field'] = 'hhhh';
            $data['charset'] = $this->language->get('charset');
            $data['language'] = $this->language->get('code');
            $data['direction'] = $this->language->get('direction');
            $data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
            $data['text_response'] = $this->language->get('text_response');
            $data['text_success'] = $this->language->get('text_success');
            $data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
            $data['text_failure'] = $this->language->get('text_failure');
            $data['text_cancelled'] = $this->language->get('text_cancelled');
            $data['text_cancelled_wait'] = sprintf($this->language->get('text_cancelled_wait'), $this->url->link('checkout/cart'));
            $data['text_pending'] = $this->language->get('text_pending');
            $data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/cart'));

            $this->load->model('checkout/order');
            $orderid = $this->request->post['txnid'] - 9909110;

            $key = $this->request->post['key'];
            $amount = $this->request->post['amount'];
            $productInfo = $this->request->post['productinfo'];
            $firstname = $this->request->post['firstname'];
            $email = $this->request->post['email'];
            $salt = $this->config->get('payment_payu_salt');
            $txnid = $this->request->post['txnid'];
            $keyString = $key . '|' . $txnid . '|' . $amount . '|' . $productInfo . '|' . $firstname . '|' . $email . '||||||||||';
            $keyArray = explode("|", $keyString);
            $reverseKeyArray = array_reverse($keyArray);
            $reverseKeyString = implode("|", $reverseKeyArray);

            if (isset($this->request->post['status']) && $this->request->post['status'] == 'success') {

                $saltString = $salt . '|' . $this->request->post['status'] . '|' . $reverseKeyString;
                $sentHashString = strtolower(hash('sha512', $saltString));
                $responseHashString = $this->request->post['hash'];

                $order_id = $this->request->post['txnid'];
                $message = '';
                $message .= 'orderId: ' . $this->request->post['txnid'] . "\n";
                $message .= 'Transaction Id: ' . $this->request->post['mihpayid'] . "\n";
                foreach ($this->request->post as $k => $val) {
                    $message .= $k . ': ' . $val . "\n";
                }
                if ($sentHashString == $responseHashString) {

                    if ($this->request->post['unmappedstatus'] == 'captured') {

                        $payu_captured_order_status_id = $this->config->get('payment_payu_captured_order_status_id');
                        $this->model_checkout_order->addOrderHistory($orderid, $payu_captured_order_status_id);
                    } elseif ($this->request->post['unmappedstatus'] == 'auth') {
                        $payu_auth_order_status_id = $this->config->get('payment_payu_auth_order_status_id');
                        $this->model_checkout_order->addOrderHistory($orderid, $payu_auth_order_status_id);
                    }

                    $this->model_checkout_order->addOrderHistory($this->request->post['txnid'], $this->config->get('payment_payu_order_status_id'), $message, false);
                    $data['continue'] = $this->url->link('checkout/success');
                    $data['column_left'] = $this->load->controller('common/column_left');
                    $data['column_right'] = $this->load->controller('common/column_right');
                    $data['content_top'] = $this->load->controller('common/content_top');
                    $data['content_bottom'] = $this->load->controller('common/content_bottom');
                    $data['footer'] = $this->load->controller('common/footer');
                    $data['header'] = $this->load->controller('common/header');

                    $this->response->setOutput($this->load->view('extension/payment/payu_success', $data));
                }
            } else {

                $data['continue'] = $this->url->link('checkout/cart');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['column_right'] = $this->load->controller('common/column_right');
                $data['content_top'] = $this->load->controller('common/content_top');
                $data['content_bottom'] = $this->load->controller('common/content_bottom');
                $data['footer'] = $this->load->controller('common/footer');
                $data['header'] = $this->load->controller('common/header');

                if (isset($this->request->post['status']) && $this->request->post['unmappedstatus'] == 'userCancelled') {
                    $payu_user_cancelled_order_status_id = $this->config->get('payment_payu_user_cancelled_order_status_id');
                    $this->model_checkout_order->addOrderHistory($orderid, $payu_user_cancelled_order_status_id);

                    $payu_cancelled_order_status_id = $this->config->get('payment_payu_cancelled_order_status_id');
                    $this->model_checkout_order->addOrderHistory($orderid, $payu_cancelled_order_status_id);

                    $this->response->setOutput($this->load->view('extension/payment/payu_cancelled', $data));
                } else {
                    $this->response->setOutput($this->load->view('extension/payment/payu_failure', $data));
                }
            }
        }

        if ($this->request->post['unmappedstatus'] == 'initiated') {

            $payu_initiated_order_status_id = $this->config->get('payment_payu_initiated_order_status_id');
            $this->model_checkout_order->addOrderHistory($orderid, $payu_initiated_order_status_id);
        } elseif ($this->request->post['unmappedstatus'] == 'in progress') {
            $payu_inprogress_order_status_id = $this->config->get('payment_payu_inprogress_order_status_id');
            $this->model_checkout_order->addOrderHistory($orderid, $payu_inprogress_order_status_id);
        } elseif ($this->request->post['unmappedstatus'] == 'dropped') {
            $payu_dropped_order_status_id = $this->config->get('payment_payu_dropped_order_status_id');
            $this->model_checkout_order->addOrderHistory($orderid, $payu_dropped_order_status_id);
        } elseif ($this->request->post['unmappedstatus'] == 'bounced') {
            $payu_bounced_order_status_id = $this->config->get('payment_payu_bounced_order_status_id');
            $this->model_checkout_order->addOrderHistory($orderid, $payu_bounced_order_status_id);
        } elseif ($this->request->post['unmappedstatus'] == 'failed') {
            $payu_failed_order_status_id = $this->config->get('payment_payu_failed_order_status_id');
            $this->model_checkout_order->addOrderHistory($orderid, $payu_failed_order_status_id);
        } elseif ($this->request->post['unmappedstatus'] == 'pending') {
            $payu_pending_order_status_id = $this->config->get('payment_payu_pending_order_status_id');
            $this->model_checkout_order->addOrderHistory($orderid, $payu_pending_order_status_id);
        }

        $sql2 = "UPDATE " . DB_PREFIX . "order SET custom_field = 'mihpayid :-" . $this->request->post['mihpayid'] . "' WHERE order_id= '" . $orderid . "'";
        $this->db->query($sql2);
    }

    /**
     * Test if the current browser runs on a mobile device (smart phone, tablet, etc.)
     *
     * @return bool
     **/
    private static function is_mobile() {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $is_mobile = false;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }

        return $is_mobile;
    }

}
