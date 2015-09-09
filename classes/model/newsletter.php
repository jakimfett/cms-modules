<?php

/**
 * Database interface class for the Newsletter
 */
class Model_Newsletter extends Model
{

    /**
     * Subscribe to the newsletter, given an email address and several optional params
     *
     * @param string $email_address email address, required
     * @param string $name subscriber's name, if given
     * @param int $ip IP address
     * @param string $city city name
     * @param string $region region name
     * @return Database result object
     */
    public function subscribe_user_by_email($email_address, $name = NULL, $ip = NULL, $city = NULL, $region = NULL)
    {
        return DB::insert('newsletter', array('email', 'name', 'ip', 'city', 'region'))->values(array($email_address, $name, $ip, $city, $region))->execute();
    }

    /**
     * Check whether an email address is already in the database
     *
     * @param string $email_address
     * @return Database result object
     */
    public function check_duplicate_email($email_address)
    {
        return DB::select('id')->from('newsletter')->where('email', '=', $email_address)->execute();
    }

    /**
     * Send email confirmations to user and admin
     * 
     * @param type $email_address
     * @param type $name
     * @return boolean
     */
    public function send_email_confirmations($email_address, $name = NULL)
    {
        if ($this->_send_confirmation_email($email_address, $name)) {
            $this->_send_admin_confirmation_email($email_address, $name);
            return TRUE;
        }

        return FALSE;
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

        $template = 'pages/newsletter-subscribe-confirm';

        $view->text = Post::dcache(Path::lookup($template)['id'], 'page', Config::load('pages'))->body;

        $email->message($view, "text/html");
        return $email->send();
    }

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
     * Add a comment for a specific email address
     *
     * @param string $email_address
     * @param string $comment
     * @return Database result object
     */
    public function add_subscription_note_by_email($email_address, $comment)
    {
        $existing_note = $this->get_subscription_note_by_email($email_address)->as_array();
        $new_comment   = '';
        if (is_array($existing_note)) {
            if (!empty($existing_note[0]['note'])) {
                $new_comment = array_values($existing_note)[0]['note'] . '\n\r' . $comment;
            } else {
                $new_comment = $comment;
            }
        }
        return DB::update('newsletter')->where('email', '=', $email_address)->set(array('note' => $new_comment))->execute();
    }

    /**
     * Get a note for a given email address
     *
     * @param string $email_address
     * @return Database result object
     */
    public function get_subscription_note_by_email($email_address)
    {
        return DB::select('note')->from('newsletter')->where('email', '=', $email_address)->execute();
    }

    /**
     * Set the opt-out flag for an email address
     *
     * @param string $email_address
     * @param boolean $boolean
     * @return Database result object
     */
    public function set_opt_out($email_address, $boolean)
    {
        return DB::update('newsletter')->where('email', '=', $email_address)->set(array('opt_out' => $boolean))->execute();
    }

    /**
     * Get the opt-out flag for an email address
     *
     * @param string $email_address
     * @return Database result object
     */
    public function get_opt_out($email_address)
    {
        return DB::select('opt_out')->from('newsletter')->where('email', '=', $email_address)->execute();
    }

    /**
     * Get all the subscribers, without filtering
     *
     * @return Database result object
     */
    public function get_all_subscribers()
    {
        return DB::select()->from('newsletter')->orderBy('date', 'asc')->execute();
    }

}
