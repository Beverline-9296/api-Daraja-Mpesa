<?php
    session_start();
    function db_conn(){
        $db_username = 'root';
        $db_password = '';
        try {
            $conn = new PDO('mysql:host=localhost;dbname=stkpush', $db_username, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            die("Fatal Error: Connection Failed! " . $e->getMessage());
        }
    }

    function mpesa($phone, $amount, $ordernum){
        #callback url - Use ngrok or a public URL for testing
        define("CALLBACK_URL",'https://your-domain.com/darajaapi/callback_url.php?orderid=');
        #access token
        $consumerKey ='8PfOKBuVAGUGpiMbnV8QJQXJGhJJPjNRdxAi0KzdvLDQKbdR';
        $consumerSecret = 'yNDAKRgi6u8nm9ILceeap9AUOuD6KRmQhdw4VQGPg5M1NMwjOfVe0AaYvG7zR8ey';

        #providing the following details, this part is found on test credentials
        $BusinessShortCode = '174379';
        $PassKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $phone = preg_replace('/^0/','254',str_replace("+","",$phone));
        $PartyA = $phone; //this is your phone number
        $PartyB ='174379';
        $TransactionDesc = 'pay order';//insert your own description
        # get the timestamp
        $Timestamp = date('YmdHis');
        #Get the base64 encoded string -> $password. The Passkey is the mpesa public
        $password = base64_encode($BusinessShortCode.$PassKey.$Timestamp);
        #header for access token
        $headers = ['Content-Type:application/json;charset=utf8'];

        #Mpesa endpoint urls
        $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $curl = curl_init($access_token_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        if (curl_error($curl)) {
            error_log('cURL Error: ' . curl_error($curl));
            curl_close($curl);
            return 'CURL_ERROR';
        }
        
        $result = json_decode($result);
        
        if (!isset($result->access_token)) {
            error_log('Access Token Error: ' . json_encode($result));
            curl_close($curl);
            return 'TOKEN_ERROR';
        }
        
        $access_token = $result->access_token;

        curl_close($curl);
        #header for stk push
        $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];
        #initiating the transaction
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $initiate_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        $curl_post_data = array(
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $Timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)$amount,
            'PartyA' => $PartyA,
            'PartyB' => $BusinessShortCode,
            'PhoneNumber' => $PartyA,
            'CallBackURL' => CALLBACK_URL . $ordernum,
            'AccountReference' => $ordernum,
            'TransactionDesc' => $TransactionDesc
        );

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        $curl_response = curl_exec($curl);
        
        if (curl_error($curl)) {
            error_log('STK Push cURL Error: ' . curl_error($curl));
            curl_close($curl);
            return 'CURL_ERROR';
        }
        
        curl_close($curl);
        
        $response = json_decode($curl_response, true);
        error_log('M-Pesa Response: ' . $curl_response);
        
        if (isset($response['ResponseCode'])) {
            return $response['ResponseCode'];
        } else {
            error_log('Invalid M-Pesa Response: ' . $curl_response);
            return 'INVALID_RESPONSE';
        }
    }
?>