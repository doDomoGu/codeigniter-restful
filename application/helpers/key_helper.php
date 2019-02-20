<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('generate_code')) {

    function generate_code()
    {
        $chars = 'klmnvXYojAB15w23LRSCD0GTUtuZMcdINOPxFHzabJpqrs4KyefghiVW67EQ89';
        $code = '';
        for ($i = 0; $i < 10; $i++) {
            $code .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $code;
    }
}
