<?php
  $API_BASE = 'https://feelme.dulta.net/api/v1';
  $PARTNER_KEY = '<please enter your key>';
  $USER_ID = 'some-user-id';

  // Get partner token
  $string = file_get_contents("{$API_BASE}/partner/{$PARTNER_KEY}/token");
  $json = json_decode($string, true);
  $partner_token = $json['partner_token'];

  if ($_POST['status']) {
    // Getting user status (online/offline, etc)
    $status = file_get_contents("{$API_BASE}/user/{$USER_ID}/status?partner_token={$partner_token}");
    print("User status: {$status}");
  } else if ($_POST['barcode']) {
    // Get barcode
    $string = file_get_contents("{$API_BASE}/user/{$USER_ID}/auth?partner_token={$partner_token}");
    $json = json_decode($string, true);
    $auth_token = $json['auth_token'];

    // Show the QR code
    print("<img src=\"http://api.qrserver.com/v1/create-qr-code/?data={$auth_token}\">");
    print("<br/><textarea disabled cols='80'>$auth_token</textarea>");
  } else if ($_POST['tip']) {
    // Send the tip
    $curl = curl_init("{$API_BASE}/user/{$USER_ID}/tip?partner_token={$partner_token}");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
      'sender' => 'John Doe', // Who sent the tip
      'amount' => '10 coins', // Tip amount
      'duration' => '2.5',    // Diration in seconds
      'strength' => '50'      // Vibration strength, percent
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);

    echo "Tip sent, response: $response";
  }
?>

<hr/>
<h4>API base: <?php echo $API_BASE; ?></h4>
<h4>User ID: <?php echo $USER_ID; ?></h4>

<form method="post">
  <input type="hidden" name="barcode" value="1"/>
  <input type="submit" value="Show the barcode"/>
</form>

<form method="post">
  <input type="hidden" name="status" value="1"/>
  <input type="submit" value="Obtain user status"/>
</form>

<form method="post">
  <input type="hidden" name="tip" value="1"/>
  <input type="submit" value="Send the tip"/>
</form>

