<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailController extends Controller
{
    public function sendContactForm(Request $request){
        $mgClient = new \Mailgun\Mailgun('30c07d5fe8a7b1604f1603c51eb0d881-059e099e-f0360647');
        $domain = "sandbox77ef6c90e41c452bb76aa8a610d044ed.mailgun.org";

        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $region = $request->get('region');
        $message = $request->get('message');

        if (isset($name) && isset($email) && isset($phone) && isset($region) && isset($message)){
            $result = $mgClient->sendMessage($domain, array(
                'from'	=> 'Excited User mailgun@sandbox77ef6c90e41c452bb76aa8a610d044ed.mailgun.org',
                'to'	=> 'test@gmail.com',
                'subject' => 'Contact Us form',
                'text'	=> "
                Name: $name
                Email: $email
                Phone: $phone
                Region: $region
                Message: $message
            "
            ));
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Not all fields were received']);
    }
}
