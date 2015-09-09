<?php

class Controller_Newsletter extends Controller
{

    private $_model = NULL;

    public function before()
    {
        parent::before();
        $this->_model = Model::factory("Newsletter");
    }

    public function action_subscribe()
    {
        $email_address = $this->request->post('email');
        $name          = $this->request->post('name');
        $ip_raw        = $this->request->post('ip');

        if (isset($email_address)) {

            $duplicate_check = $this->_model->check_duplicate_email($email_address);
            if ($duplicate_check->count() !== 0) {
                die('DUPLICATE');
            } else {
                $city   = null;
                $region = null;
                if (!filter_var($ip_raw, FILTER_VALIDATE_IP) === false) {

                    $details = json_decode(file_get_contents("http://ipinfo.io/{$ip_raw}/json"));

                    if (isset($details->city)) {
                        $city = $details->city;
                    }
                    if (isset($details->region)) {
                        $region = $details->region;
                    }
                    /**
                     * Note the conversion of the IP address using ip2long
                     * @link http://daipratt.co.uk/mysql-store-ip-address/
                     */
                    $insert = $this->_model->subscribe_user_by_email($email_address, $name, ip2long($ip_raw), $city, $region);
                } else {
                    $insert = $this->_model->subscribe_user_by_email($email_address, $name);
                }


                if (isset($insert[0])) {
                    if ($insert[0] > 0) {
                        if ($this->_model->send_email_confirmations($email_address, $name)) {
                            die('SUBSCRIBED');
                        }
                    }
                }
            }
            die('FAILURE');
        } else {
            header("HTTP/1.1 401 Unauthorized");
            die('<h1>Direct Access Denied</h1>');
        }
    }

    public function action_unsubscribe()
    {
        $email_address = $this->request->post('email');
        if (isset($email_address)) {
            $duplicate_check = $this->_model->check_duplicate_email($email_address);
            if ($duplicate_check->count() === 0) {
                die('UNREGISTERED');
            } else {
                $opt_out_check = $this->_model->get_opt_out($email_address);
                if (isset($opt_out_check->as_array()[0]['opt_out'])) {
                    if ($opt_out_check->as_array()[0]['opt_out'] == 0) {
                        if ($this->_model->set_opt_out($email_address, TRUE)) {
                            $this->_model->add_subscription_note_by_email($email_address, $this->request->post('note'));
                            die('UNSUBSCRIBED');
                        }
                    } else {
                        die("REUNSUBSCRIBED");
                    }
                }
            }
        }
    }

    public function action_list()
    {
        $view              = View::factory('admin/newsletter-subscribers-list');
        $view->subscribers = $this->_model->get_all_subscribers();
        $this->response->body($view);
    }

}
