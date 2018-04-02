<?php
  // Key to prevent hackers from potentially controlling your TV!
  $secret_key = '1234567890';

  // CEC IDs of equipment. Can be single ID or multiple (ie: if also shutting off sound bar)
  $cec_ids = [0,5];

  $json = file_get_contents('php://input');
  $request = json_decode($json, true);
  process_request($request);

  function process_request($request) {
    global $cec_ids;

    if (key_authenticated($request['key'])) {
      switch(strtolower($request['type'])) {
        case 'power':
          toggle_power(strtolower($request['command']));
          break;
      }
    }
  }

  function toggle_power($command) {
    global $cec_ids;

    foreach ($cec_ids as $cec_id) {
      switch($command) {
        case 'on':
          echo shell_exec("echo 'on $cec_id' | sudo /usr/bin/cec-client -s");
          break;
        case 'off':
          echo shell_exec("echo 'standby $cec_id' | sudo /usr/bin/cec-client -s");
          break;
      }
    }
  }

  function key_authenticated($key) {
    global $secret_key;

    if ($key == $secret_key) {
      return true;
    } else {
      return false;
    }
  }
?>
