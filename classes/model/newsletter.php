<?php

class Model_Newsletter extends Model {

    public function subscribe_user_by_email($email_address, $name = NULL, $ip = NULL, $city = NULL, $region = NULL) {
        return DB::insert('newsletter', array('email', 'name', 'ip', 'city', 'region'))->values(array($email_address, $name, $ip, $city, $region))->execute();
    }

    public function check_duplicate_email($email_address) {
        return DB::select('id')->from('newsletter')->where('email', '=', $email_address)->execute();
    }

    public function add_subscription_note_by_email($email_address, $comment) {
        $existing_note = $this->get_subscription_note_by_email($email_address)->as_array();
        $new_comment = '';
        if (is_array($existing_note)) {
            if (!empty($existing_note[0]['note'])) {
                $new_comment = array_values($existing_note)[0]['note'] . '\n\r' . $comment;
            } else {
                $new_comment = $comment;
            }
        }
        return DB::update('newsletter')->where('email', '=', $email_address)->set(array('note' => $new_comment))->execute();
    }

    public function get_subscription_note_by_email($email_address) {
        return DB::select('note')->from('newsletter')->where('email', '=', $email_address)->execute();
    }

    public function set_opt_out($email_address, $boolean) {
        return DB::update('newsletter')->where('email', '=', $email_address)->set(array('opt_out' => $boolean))->execute();
    }

    public function get_opt_out($email_address) {
        return DB::select('opt_out')->from('newsletter')->where('email', '=', $email_address)->execute();
    }
    
    public function get_all_subscribers(){
        return DB::select()->from('newsletter')->execute();
    }

}
