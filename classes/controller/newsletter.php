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
        if (isset($email_address)) {

            $duplicate_check = $this->_model->check_duplicate_email($email_address);
            if ($duplicate_check->count() !== 0) {
                die('DUPLICATE');
            } else {
                $insert = $this->_model->subscribe_user_by_email($email_address, $name);
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

    private function _send_admin_confirmation_email($email_address, $name = NULL)
    {
        $admin_email = Email::factory();

        $admin_email->subject("Newsletter Subscription Confirmation");
        $admin_email->to("jakimfett@gmail.com");
        $admin_email->from("noreply@solvethelabyrinth.com", "Labyrinth");
        $message = "Newsletter subscriber<br/>email: " . $email_address;
        if (isset($name)) {
            $message .= "<br/>name: " . $name;
        }
        $admin_email->message($message, "text/html");
        return $admin_email->send();
    }

    public function action_list()
    {
        $view                         = View::factory('admin/newsletter-subscribers-list');
        $view->subscribers            = $this->_model->get_all_subscribers();
        $this->response->body($view);
    }

}
