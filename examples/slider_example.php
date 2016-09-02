<?php
  $user_id = $_GET['userid'];
  if (!$user_id) {
    header('Location: ' . $_SERVER["PHP_SELF"] . '?userid=' . rand(1111, 9999));
    die();
  }

  $FEEL_APPS_SERVER_PATH = 'https://feelme.dulta.net';
  // Obtain Feel Apps partner token
  //
  $PARTNER_KEY = '<your partner key>'; // Replace with your own Feel Apps partner key
  $string = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/partner/{$PARTNER_KEY}/token");
  $json = json_decode($string, true);
  $feel_apps_token = $json['partner_token'];

  // Obtain QR code
  //
  $string = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/user/{$user_id}/auth?partner_token={$feel_apps_token}");
  $json = json_decode($string, true);
  $qrcode = $json['auth_token'];
?><html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
</head>
<body>
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand">
          <img src="feel-logo.png" style="display: inline-block; height: 30px; margin-top: -5px"/>
          FeelApps Slider example
        </a>
      </div>
    </div>
  </nav>

  <div class="container" role="main" style="padding-top: 60px;">
    <div class="row" id="disconnected-div" style='display: none;'>
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">User <b><?php echo $user_id; ?></b> is not connected.
            Please start the app and scan the QR code below if you didn't scan it yet</h3>
          </div>
          <div class="panel-body">
            <center>
              <p><img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $qrcode; ?>"></p>
            </center>
          </div>
        </div>
      </div>
    </div>

    <div class="row" id="connected-div" style='display: none;'>
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">Slide to control connected devices</h3>
          </div>
          <div class="panel-body">
            <div id="slider"></div>
            <hr/>
            <p>User <b><?php echo $user_id; ?></b> is connected</p>
            <p>Connected devices: <span id="userdevices"></span></p>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script type="text/javascript" src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="<?php print $FEEL_APPS_SERVER_PATH; ?>/static/feeljs/1.0/feel.min.js"></script>
  <script>
    // Initialize slider control UI
    $( "#slider" ).slider({
      min: 0,
      max: 4,
      step: 1,
      value: 0,
      slide: function( event, ui ) {
        var percent = ui.value*100/4
        // Send slider event to the connected mobile app
        $feel.apps.playSubtitle(percent)
      }
    })

    // Initialize the JS SDK to be used with slider.
    // Set Feel Apps partner token and current user id
    var FEEL_APPS_TOKEN = '<?php echo $feel_apps_token; ?>'
    var FEEL_APPS_USER_ID = '<?php echo $user_id; ?>'
    $feel.initSlider(FEEL_APPS_TOKEN, FEEL_APPS_USER_ID)

    // Subscribe to user online status changes
    $feel.apps.status.subscribe(function (userStatus) {
      // Update the list of connected devices
      $('#userdevices').text(userStatus.devices)

      // Indicate if user is online or offline
      if (userStatus.online) {
        $('#connected-div').show()
        $('#disconnected-div').hide()
      } else {
        $('#connected-div').hide()
        $('#disconnected-div').show()
      }
    })
  </script>
</body>
</html>
