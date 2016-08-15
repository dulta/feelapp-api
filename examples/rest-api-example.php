<?php
  $API_BASE = 'https://feelme.dulta.net/api/v1';
  $PARTNER_KEY = '<please enter your key>';
  $user_id = $_GET['userid'];
  if (!$user_id) {
    header('Location: ' . $_SERVER["PHP_SELF"] . '?userid=' . rand(1111, 9999));
    die();
  }

  $room_id = $_POST['room'] ? $_POST['room'] : '';

  // Get partner token
  $url = "{$API_BASE}/partner/{$PARTNER_KEY}/token";
  $string = file_get_contents($url);
  $json = json_decode($string, true);
  $partner_token = $json['partner_token'];
  $servier_response = '';

  if ($_POST['status']) {
    // Getting user status (online/offline, etc)
    $status = file_get_contents("{$API_BASE}/user/{$user_id}/status?partner_token={$partner_token}");
    $servier_response = "<pre>{$status}</pre>";
  } else if ($_POST['barcode']) {
    // Get barcode
    $string = file_get_contents("{$API_BASE}/user/{$user_id}/auth?partner_token={$partner_token}");
    $json = json_decode($string, true);
    $auth_token = $json['auth_token'];

    // Show the QR code
    $servier_response = "<center>
      <p><img src=\"http://api.qrserver.com/v1/create-qr-code/?data={$auth_token}\"></p>
      <p><textarea disabled cols='60'>$auth_token</textarea></p>
      </center>";

  } else if ($_POST['tip']) {
    // Send the tip
    $curl = curl_init("{$API_BASE}/user/{$user_id}/tip?partner_token={$partner_token}");
    curl_setopt($curl, CURLOPT_POST, true);
    $post_fields =array(
      'sender' => $_POST['sender'],     // Who sent the tip
      'amount' => $_POST['amount'],     // Tip amount
      'duration' => $_POST['duration'], // Duration in seconds
      'strength' => $_POST['strength'],  // Vibration strength, percent
      'customText' => $_POST['customText'] //Custom message
    ) ;
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);

    $servier_response = "Tip sent, server response: $response";
  } else if ($_POST['add_user']) {
    // Add user to the room
    $room_id = urlencode($_POST['room']);
    if ($room_id) {
      $curl = curl_init("{$API_BASE}/room/{$room_id}/users?partner_token={$partner_token}");
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, array(
        'user' => $user_id,         // User to add
        'read' => $_POST['read'],   // Can user read from the room?
        'write' => $_POST['write']  // Can user write to the room?
      ));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($curl);
      curl_close($curl);

      $servier_response = "Joining the room, server response: $response";
    } else {
      $servier_response = "Room ID should not be empty";
    }
  } else if ($_POST['kick_user']) {
    // Kick user out of the room
    $room_id = urlencode($_POST['room']);
    if ($room_id) {
      $curl = curl_init("{$API_BASE}/room/{$room_id}/users/{$user_id}?partner_token={$partner_token}");
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($curl);
      curl_close($curl);

      $servier_response = "Kicking out of the room, server response: $response";
    } else {
      $servier_response = "Room ID should not be empty";
    }
  } else if ($_POST['rooms_with_users']) {
    $servier_response = file_get_contents("{$API_BASE}/rooms_with_users?partner_token={$partner_token}");
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>FeelApps REST API Test</title>

    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">

          <a class="navbar-brand">
            <img src="feel-logo.png" style="display: inline-block; height: 30px; margin-top: -5px"/>
            FeelApps REST API Test
          </a>
        </div>
      </div>
    </nav>

    <div class="container" role="main" style="padding-top: 60px;">

      <?php if ($servier_response) { ?>
        <div class="row">
          <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-success">
              <div class="panel-heading">
                <h3 class="panel-title">API call result</h3>
              </div>
              <div class="panel-body">
                <?php echo $servier_response; ?>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

      <div class="row">
        <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">General</h3>
            </div>
            <div class="panel-body">
              <h4>API base: <?php echo $API_BASE; ?></h4>
              <h4>User ID: <?php echo $user_id; ?></h4>
              <p><a href="https://github.com/dulta/feelme-api">FeelMe REST API documentation</a></p>

              <hr/>
              <form method="post">
                <input type="hidden" name="barcode" value="1"/>
                <input type="submit" value="Show the QR code" class="btn btn-lg btn-default"/>
              </form>
              <hr/>
              <form method="post">
                <input type="hidden" name="status" value="1"/>
                <input type="submit" value="Obtain user status" class="btn btn-lg btn-default"/>
              </form>
            </div>
          </div>
        </div>


        <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Tip</h3>
            </div>
            <div class="panel-body">
              <form method="post">
                <div class="form-group">
                  <label for="sender">Sender (string)</label>
                  <input type="text" id="sender" name="sender" class="form-control"
                    value="<?php echo $_POST['sender'] ? $_POST['sender'] : 'John Doe'; ?>"/>
                </div>

                <div class="form-group">
                  <label for="amount">Amount (string)</label>
                  <input type="text" id="amount" name="amount" class="form-control"
                    value="<?php echo $_POST['amount'] ? $_POST['amount'] : '10 coins'; ?>"/>
                </div>


                <div class="form-group">
                  <label for="duration">Duration (float)</label>
                  <input type="text" id="duration" name="duration" class="form-control"
                    value="<?php echo $_POST['duration'] ? $_POST['duration'] : 2.5; ?>"/>
                </div>

                <div class="form-group">
                  <label for="strength">Strength (percents)</label>
                  <input type="text" id="strength" name="strength" class="form-control"
                    value="<?php echo $_POST['strength'] ? $_POST['strength'] : 50; ?>"/>
                </div>

                <div class="form-group">
                  <label for="customText">Custom message (string)</label>
                  <input type="text" id="customText" name="customText" class="form-control"
                    value="<?php echo $_POST['customText'] ? $_POST['customText'] : ''; ?>"/>
                </div>

                <input type="hidden" name="tip" value="1"/>
                <input type="submit" value="Send the tip" class="btn btn-lg btn-default"/>
              </form>
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Rooms</h3>
            </div>
            <div class="panel-body">
              <form method="post">
                <div class="form-group">
                  <label for="room">Room ID (string)</label>
                  <input type="text" name="room" id="room" class="form-control"
                    value="<?php echo $room_id; ?>"/>
                  <input type="checkbox" name="read" id="read" value="1"/> <label for="read">read</label>
                  <input type="checkbox" name="write" id="write" value="1"/><label for="write">write</label>
                </div>
                <input type="hidden" name="add_user" value="1"/>
                <input type="submit" value="Add user to the room" class="btn btn-lg btn-default"/>
              </form>

              <hr/>
              <form method="post">
                <div class="form-group">
                  <label for="room">Room ID (string)</label>
                  <input type="text" name="room" id="room" class="form-control"
                    value="<?php echo $room_id; ?>"/>
                </div>
                <input type="hidden" name="kick_user" value="1"/>
                <input type="submit" value="Kick user out of the room" class="btn btn-lg btn-default"/>
              </form>
              <hr/>
              <form method="post">
                <input type="hidden" name="rooms_with_users" value="1"/>
                <input type="submit" value="Get all rooms with users" class="btn btn-lg btn-default">
              </form>
            </div>
            </div>
          </div>

      </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

  </body>
</html>
