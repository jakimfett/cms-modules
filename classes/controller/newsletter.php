<?php
/**
 * Controller class for the Newsletter module
 */
class Controller_Newsletter extends Controller
{

    /**
     * Global variable for Newsletter model
     * @var type Model_Newsletter
     */
    private $_model = NULL;

    /**
     * Execute when model is instantiated
     */
    public function before()
    {
        parent::before();
        $this->_model = Model::factory("Newsletter");
    }

    /**
     * Newsletter subscribe page
     */
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
                        if ($this->_send_confirmation_email($email_address, $name)) {
                            $this->_send_admin_confirmation_email($email_address, $name);
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

    /**
     * Unsubscribe page 
     */
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

    /**
     * Send a confirmation email to an email address
     * @param type $email_address
     * @param type $name
     * @return type Boolean
     */
    private function _send_confirmation_email($email_address, $name = NULL)
    {
        $email               = Email::factory();
        $email->subject("Newsletter Subscription Confirmation");
        $email->to($email_address);
        $email->from("noreply@solvethelabyrinth.com", "Labyrinth");
        $view                = View::factory('email/newsletter-subscribe-confirmation');
        $view->email_address = $email_address;

        if (isset($name)) {
            $view->name = $name;
        }
        $email->message($view, "text/html");
        return $email->send();
    }

    /**
     * Send notification to the administer about a subscriber
     * @param type $email_address
     * @param type $name
     * @return type Boolean
     */
    private function _send_admin_confirmation_email($email_address, $name = NULL)
    {
        $admin_email = Email::factory();

        $admin_email->subject("Newsletter Subscription Confirmation");
        $admin_email->to("webmaster@solvethelabyrinth.com");
        $admin_email->from("noreply@solvethelabyrinth.com", "Labyrinth");
        $message = "Newsletter subscriber<br/>email: " . $email_address;
        if (isset($name)) {
            $message .= "<br/>name: " . $name;
        }
        $admin_email->message($message, "text/html");
        return $admin_email->send();
    }

    /**
     * List all subscribers, without filter
     */
    public function action_list()
    {
        $view              = View::factory('admin/newsletter-subscribers-list');
        $view->subscribers = $this->_model->get_all_subscribers();
        $this->response->body($view);
    }

}
