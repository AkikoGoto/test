<?php
     
    $url = 'https://android.apis.google.com/c2dm/send';
     
  //  $registration_id = 'XXXXXXXXXXXXXXXXXXXXXXXX'; // registration id
    $message = '‚ ‚¢‚¤‚¦‚¨‚©‚«‚­‚¯‚±';
     
    $header = array(
      'Content-type: application/x-www-form-urlencoded',
      'Authorization: GoogleLogin auth=DQAAAP4AAAD9Sx9u-7XPq3SyVq06Bofg1pPAA-x75Te7R7LvHH0iZNghfHf-ETbJAZAlbyx-Sf-saPmKa-Wu25q_KQzTCnCrwLTt9TP07sfK1Sh14E0upix77AACzx6beqh4feKFI5WjU7PFYNcwAJDioY9Q-a4bduoKCnVu1KjMTfbGfq2FdQYxrCb9L-tEuNAHjxCicsDqsy6Hng0C079GELJC4EcAiYMQIoqU7kbeOQxKmhqu0_eoRzw-4h5EaVBckGJIt5I4Zapk_FO1BfLmPJNO2rWv2a_Auh2ElwKoBOr2IRMVXPaAC1qrUTQ0p4NUYWQ9EC6fFfGbEBPNpETFR5r-6aol', // ”FØToken
    );
    $post_list = array(
      'registration_id' => $registration_id,
      'collapse_key' => 1,
      'data.message' => $message,
    );
    $post = http_build_query($post_list, '&');
     
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $ret = curl_exec($ch);
     
    var_dump($ret);

?>