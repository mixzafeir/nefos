<?php

    function get_string_between($string, $start, $end){
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);   
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
    }

    // Returns Subject Token
    function keyrockLoginSuperAdmin() 
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://172.28.1.5:3000/v1/auth/tokens',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_HEADER => 1,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
          "name": "mixzafeir@gmail.com",
          "password": "12345678"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: session=eyJyZWRpciI6Ii8ifQ==; session.sig=TqcHvLKCvDVxuMk5xVfrKEP-GSQ'
          ),
        ));
        
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        //var_dump($header);

        curl_close($curl);

        return get_string_between($header, "X-Subject-Token: ", " Content-Type");



    }

    // Returns Access Token - OAuth2
    function keyrockLogin($username, $password)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://172.28.1.5:3000/oauth2/token',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'grant_type=password&username='.$username.'&password='.$password,
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ZmJhMTcyZTMtYzY2Yi00ZTBjLTk0YjUtZjBiOGEyNWI0YTMzOmJmZWZmNTM0LWRkNmEtNGZhMy1iYzc4LWRlNTM0NWVmZTYyZA==',
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: session=eyJyZWRpciI6Ii8ifQ==; session.sig=TqcHvLKCvDVxuMk5xVfrKEP-GSQ'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);

        return get_string_between($response, '"access_token":"', '"');
    }

    function keyrockCreateUser($username, $email, $password)
    {

        $subject_token=keyrockLoginSuperAdmin();
        //var_dump($subject_token);


        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://172.28.1.5:3000/v1/users',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'',
          CURLOPT_POSTFIELDS =>'{
            "user" : {
                "username": "' . $username . '",
                "email": "' . $email . '",
                "password": "' . $password . '"
            }
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'X-Auth-token: ' . $subject_token,
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }
?>