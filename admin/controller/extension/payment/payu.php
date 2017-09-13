<?php


class ControllerExtensionPaymentPayu extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/payment/payu');
		
        $this->document->setTitle($this->language->get('heading_title'));
		
        $this->load->model('setting/setting');
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_payu', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/payment/payu', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }
		
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_live'] = $this->language->get('text_live');
        $data['text_successful'] = $this->language->get('text_successful');
        $data['text_fail'] = $this->language->get('text_fail');
        $data['demo'] = $this->language->get('demo');
        $data['entry_merchant'] = $this->language->get('entry_merchant');
        $data['entry_salt'] = $this->language->get('entry_salt');
        $data['entry_test'] = $this->language->get('entry_test');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['help_password'] = $this->language->get('help_password');
        $data['help_total'] = $this->language->get('help_total');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['help_salt'] = $this->language->get('help_salt');
        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_order_status_payubiz'] = $this->language->get('tab_order_status_payubiz');
        $data['entry_captured_order_status'] = $this->language->get('entry_captured_order_status');
        $data['entry_bounced_order_status'] = $this->language->get('entry_bounced_order_status');
        $data['entry_dropped_order_status'] = $this->language->get('entry_dropped_order_status');
        $data['entry_failed_order_status'] = $this->language->get('entry_failed_order_status');
        $data['entry_user_cancelled_order_status'] = $this->language->get('entry_user_cancelled_order_status');
        $data['entry_inprogress_order_status'] = $this->language->get('entry_inprogress_order_status');
        $data['entry_initiated_order_status'] = $this->language->get('entry_initiated_order_status');
        $data['entry_auto_refund_order_status'] = $this->language->get('entry_auto_refund_order_status');
        $data['entry_pending_order_status'] = $this->language->get('entry_pending_order_status');
        $data['entry_auth_order_status'] = $this->language->get('entry_auth_order_status');
        $data['entry_cancelled_order_status'] = $this->language->get('entry_cancelled_order_status');
        $data['entry_pg'] = $this->language->get('entry_pg');
        $data['CreditCard'] = $this->language->get('CreditCard');
        $data['DebitCard'] = $this->language->get('DebitCard');
        $data['NetBanking'] = $this->language->get('NetBanking');
        $data['PayUMoney'] = $this->language->get('PayUMoney');
        $data['PayUbiz'] = $this->language->get('PayUbiz');
        $data['entry_bankcode'] = $this->language->get('entry_bankcode');

        $data['payu_pg'][0] = array("name" => "Credit Card", "value" => "CC");
        $data['payu_pg'][1] = array("name" => "Debit Card", "value" => "DC");
        $data['payu_pg'][2] = array("name" => "NetBanking", "value" => "NB");
        $data['payu_pg'][3] = array("name" => "PayUMoney", "value" => "wallet");
        $data['payu_pg'][5] = array("name" => "PayUbiz", "value" => "");

        $data['payu_bankcode'][0] = array('value' => '', 'name' => 'PayUbiz');
        $data['payu_bankcode'][1] = array('value' => 'payuw', 'name' => 'PayUw- PayUMoney');
        $data['payu_bankcode'][2] = array('value' => 'BBCB', 'name' => 'Bank of Baroda Corporate Banking');
        $data['payu_bankcode'][3] = array('value' => 'ALLB', 'name' => 'Allahabad Bank NetBanking');
        $data['payu_bankcode'][4] = array('value' => 'ADBB', 'name' => 'Andhra Bank');
        $data['payu_bankcode'][5] = array('value' => 'AXIB', 'name' => 'AXIS Bank NetBanking');
        $data['payu_bankcode'][6] = array('value' => 'BBKB', 'name' => 'Bank of Bahrain and Kuwait');
        $data['payu_bankcode'][7] = array('value' => 'BBRB', 'name' => 'Bank of Baroda Retail Banking');
        $data['payu_bankcode'][8] = array('value' => 'BOIB', 'name' => 'Bank of India');
        $data['payu_bankcode'][9] = array('value' => 'BOMB', 'name' => 'Bank of Maharashtra');
        $data['payu_bankcode'][10] = array('value' => 'CABB', 'name' => 'Canara Bank');
        $data['payu_bankcode'][11] = array('value' => 'CSBN', 'name' => 'Catholic Syrian Bank');
        $data['payu_bankcode'][12] = array('value' => 'CBIB', 'name' => 'Central Bank Of India');
        $data['payu_bankcode'][13] = array('value' => 'CITNB', 'name' => 'Citi Bank NetBanking');
        $data['payu_bankcode'][14] = array('value' => 'CUBB', 'name' => 'CityUnion');
        $data['payu_bankcode'][15] = array('value' => 'CRPB', 'name' => 'Corporation Bank');
        $data['payu_bankcode'][16] = array('value' => 'DCBCORP', 'name' => 'DCB Bank - Corporate Netbanking');
        $data['payu_bankcode'][17] = array('value' => 'DENN', 'name' => 'Dena Bank');
        $data['payu_bankcode'][18] = array('value' => 'DSHB', 'name' => 'Deutsche Bank');
        $data['payu_bankcode'][19] = array('value' => 'DCBB', 'name' => 'Development Credit Bank');
        $data['payu_bankcode'][20] = array('value' => 'FEDB', 'name' => 'Federal Bank');
        $data['payu_bankcode'][21] = array('value' => 'HDFB', 'name' => 'HDFC Bank');
        $data['payu_bankcode'][22] = array('value' => 'ICIB', 'name' => 'ICICI Netbanking');
        $data['payu_bankcode'][23] = array('value' => 'INDB', 'name' => 'Indian Bank');
        $data['payu_bankcode'][24] = array('value' => 'INOB', 'name' => 'Indian Overseas Bank');
        $data['payu_bankcode'][25] = array('value' => 'INIB', 'name' => 'IndusInd Bank');
        $data['payu_bankcode'][26] = array('value' => 'IDBB', 'name' => 'Industrial Development Bank of India');
        $data['payu_bankcode'][27] = array('value' => 'INGB', 'name' => 'ING Vysya Bank');
        $data['payu_bankcode'][28] = array('value' => 'JAKB', 'name' => 'Jammu and Kashmir Bank');
        $data['payu_bankcode'][29] = array('value' => 'KRKB', 'name' => 'Karnataka Bank');
        $data['payu_bankcode'][30] = array('value' => 'KRVB', 'name' => 'Karur Vysya');
        $data['payu_bankcode'][31] = array('value' => 'KRVB', 'name' => 'Karur Vysya - Corporate Netbanking');
        $data['payu_bankcode'][32] = array('value' => '162B', 'name' => 'Kotak Bank');
        $data['payu_bankcode'][33] = array('value' => 'LVCB', 'name' => 'Laxmi Vilas Bank-Corporate');
        $data['payu_bankcode'][34] = array('value' => 'LVRB', 'name' => 'Laxmi Vilas Bank-Retail');
        $data['payu_bankcode'][35] = array('value' => 'OBCB', 'name' => 'Oriental Bank of Commerce');
        $data['payu_bankcode'][36] = array('value' => 'PNBB', 'name' => 'Punjab National Bank - Retail Banking');
        $data['payu_bankcode'][37] = array('value' => 'CPNB', 'name' => 'Punjab National Bank-Corporate');
        $data['payu_bankcode'][38] = array('value' => 'RTN', 'name' => 'Ratnakar Bank');
        $data['payu_bankcode'][39] = array('value' => 'SRSWT', 'name' => 'Saraswat Bank');
        $data['payu_bankcode'][40] = array('value' => 'SVCB', 'name' => 'Shamrao Vitthal Co-operative Bank');
        $data['payu_bankcode'][41] = array('value' => 'SOIB', 'name' => 'South Indian Bank');
        $data['payu_bankcode'][42] = array('value' => 'SDCB', 'name' => 'Standard Chartered Bank');
        $data['payu_bankcode'][43] = array('value' => 'SBBJB', 'name' => 'State Bank of Bikaner and Jaipur');
        $data['payu_bankcode'][44] = array('value' => 'SBHB', 'name' => 'State Bank of Hyderabad');
        $data['payu_bankcode'][45] = array('value' => 'SBIB', 'name' => 'State Bank of India');
        $data['payu_bankcode'][46] = array('value' => 'SBMB', 'name' => 'State Bank of Mysore');
        $data['payu_bankcode'][47] = array('value' => 'SBPB', 'name' => ' State Bank of Patiala');
        $data['payu_bankcode'][48] = array('value' => 'SBTB', 'name' => 'State Bank of Travancore');
        $data['payu_bankcode'][49] = array('value' => 'UBIBC', 'name' => 'Union Bank - Corporate Netbanking');
        $data['payu_bankcode'][50] = array('value' => 'UBIB', 'name' => 'Union Bank of India');
        $data['payu_bankcode'][51] = array('value' => 'UNIB', 'name' => 'United Bank Of India');
        $data['payu_bankcode'][52] = array('value' => 'VJYB', 'name' => 'Vijaya Bank');
        $data['payu_bankcode'][53] = array('value' => 'YESB', 'name' => 'Yes Bank');


        if (isset($this->request->post['payment_payu_captured_order_status_id'])) {
            $data['payment_payu_captured_order_status_id'] = $this->request->post['payment_payu_captured_order_status_id'];
        } else {
            $data['payment_payu_captured_order_status_id'] = $this->config->get('payment_payu_captured_order_status_id');
        }
        if (isset($this->request->post['payment_payu_bounced_order_status_id'])) {
            $data['payment_payu_bounced_order_status_id'] = $this->request->post['payment_payu_bounced_order_status_id'];
        } else {
            $data['payment_payu_bounced_order_status_id'] = $this->config->get('payment_payu_bounced_order_status_id');
        }

        if (isset($this->request->post['payment_payu_auth_order_status_id'])) {
            $data['payment_payu_auth_order_status_id'] = $this->request->post['payment_payu_auth_order_status_id'];
        } else {
            $data['payment_payu_auth_order_status_id'] = $this->config->get('payment_payu_auth_order_status_id');
        }

        if (isset($this->request->post['payment_payu_dropped_order_status_id'])) {
            $data['payment_payu_dropped_order_status_id'] = $this->request->post['payment_payu_dropped_order_status_id'];
        } else {
            $data['payment_payu_dropped_order_status_id'] = $this->config->get('payment_payu_dropped_order_status_id');
        }

        if (isset($this->request->post['payment_payu_failed_order_status_id'])) {
            $data['payment_payu_failed_order_status_id'] = $this->request->post['payment_payu_failed_order_status_id'];
        } else {
            $data['payment_payu_failed_order_status_id'] = $this->config->get('payment_payu_failed_order_status_id');
        }

        if (isset($this->request->post['payment_payu_user_cancelled_order_status_id'])) {
            $data['payment_payu_user_cancelled_order_status_id'] = $this->request->post['payment_payu_user_cancelled_order_status_id'];
        } else {
            $data['payment_payu_user_cancelled_order_status_id'] = $this->config->get('payment_payu_user_cancelled_order_status_id');
        }

        if (isset($this->request->post['payment_payu_inprogress_order_status_id'])) {
            $data['payment_payu_inprogress_order_status_id'] = $this->request->post['payment_payu_inprogress_order_status_id'];
        } else {
            $data['payment_payu_inprogress_order_status_id'] = $this->config->get('payment_payu_inprogress_order_status_id');
        }

        if (isset($this->request->post['payment_payu_initiated_order_status_id'])) {
            $data['payment_payu_initiated_order_status_id'] = $this->request->post['payment_payu_initiated_order_status_id'];
        } else {
            $data['payment_payu_initiated_order_status_id'] = $this->config->get('payment_payu_initiated_order_status_id');
        }

        if (isset($this->request->post['payment_payu_auto_refund_order_status_id'])) {
            $data['payment_payu_auto_refund_order_status_id'] = $this->request->post['payment_payu_auto_refund_order_status_id'];
        } else {
            $data['payment_payu_auto_refund_order_status_id'] = $this->config->get('payment_payu_auto_refund_order_status_id');
        }

        if (isset($this->request->post['payment_payu_pending_order_status_id'])) {
            $data['payment_payu_pending_order_status_id'] = $this->request->post['payment_payu_pending_order_status_id'];
        } else {
            $data['payment_payu_pending_order_status_id'] = $this->config->get('payment_payu_pending_order_status_id');
        }

        if (isset($this->request->post['payment_payu_bankcode_val'])) {
            $data['payment_payu_bankcode_val'] = $this->request->post['payment_payu_bankcode_val'];
        } else {
            $data['payment_payu_bankcode_val'] = $this->config->get('payment_payu_bankcode_val');
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['merchant'])) {
            $data['error_merchant'] = $this->error['merchant'];
        } else {
            $data['error_merchant'] = '';
        }

        if (isset($this->error['salt'])) {
            $data['error_salt'] = $this->error['salt'];
        } else {
            $data['error_salt'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/payu', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: '
        );
        
        $data['action'] = $this->url->link('extension/payment/payu', 'user_token=' . $this->session->data['user_token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], 'SSL');
        
        if (isset($this->request->post['payment_payu_merchant'])) {
            $data['payment_payu_merchant'] = $this->request->post['payment_payu_merchant'];
        } else {
            $data['payment_payu_merchant'] = $this->config->get('payment_payu_merchant');
        }
        
        if (isset($this->request->post['payment_payu_salt'])) {
            $data['payment_payu_salt'] = $this->request->post['payment_payu_salt'];
        } else {
            $data['payment_payu_salt'] = $this->config->get('payment_payu_salt');
        }
        
        if (isset($this->request->post['payu_test'])) {
            $data['payment_payu_test'] = $this->request->post['payment_payu_test'];
        } else {
            $data['payment_payu_test'] = $this->config->get('payment_payu_test');
        }
        
        if (isset($this->request->post['payu_total'])) {
            $data['payment_payu_total'] = $this->request->post['payment_payu_total'];
        } else {
            $data['payment_payu_total'] = $this->config->get('payment_payu_total');
        }
        
        if (isset($this->request->post['payu_order_status_id'])) {
            $data['payment_payu_order_status_id'] = $this->request->post['payment_payu_order_status_id'];
        } else {
            $data['payment_payu_order_status_id'] = $this->config->get('payment_payu_order_status_id');
        }
        
        $this->load->model('localisation/order_status');
        
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
        if (isset($this->request->post['payment_payu_geo_zone_id'])) {
            $data['payment_payu_geo_zone_id'] = $this->request->post['payment_payu_geo_zone_id'];
        } else {
            $data['payment_payu_geo_zone_id'] = $this->config->get('payment_payu_geo_zone_id');
        }
        
        $this->load->model('localisation/geo_zone');
        
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        
        if (isset($this->request->post['payment_payu_status'])) {
            $data['payment_payu_status'] = $this->request->post['payment_payu_status'];
        } else {
            $data['payment_payu_status'] = $this->config->get('payment_payu_status');
        }
        
        if (isset($this->request->post['payment_payu_payment_gateway'])) {
            $data['payment_payu_payment_gateway'] = $this->request->post['payment_payu_payment_gateway'];
        } else {
            $data['payment_payu_payment_gateway'] = $this->config->get('payment_payu_payment_gateway');
        }

        if (isset($this->request->post['payment_payu_sort_order'])) {
            $data['payment_payu_sort_order'] = $this->request->post['payment_payu_sort_order'];
        } else {
            $data['payment_payu_sort_order'] = $this->config->get('payment_payu_sort_order');
        }
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('extension/payment/payubiz', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/payu')) {
            $this->error['warning'] = $this->language->get('error_permission');
            var_dump('hell yeah');die;
        }
        
        if (!$this->request->post['payment_payu_merchant']) {
            $this->error['merchant'] = $this->language->get('error_merchant');
        }
        
        if (!$this->request->post['payment_payu_salt']) {
            $this->error['salt'] = $this->language->get('error_salt');
        }
        
        return !$this->error;
    }

}
