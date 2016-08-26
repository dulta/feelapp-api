<?php
  $user_id = $_GET['userid'];
  if (!$user_id) {
    header('Location: ' . $_SERVER["PHP_SELF"] . '?userid=' . rand(1111, 9999));
    die();
  }

  // Obtain Feel Subs token
  //
  $FEEL_SUBS_SERVER_PATH = 'https://feel-subs.dulta.net';
  $FEEL_SUBS_APP_KEY = '<your subtitles api key>'; // Replace with your own Feel Subtitles app key
  $json = file_get_contents($FEEL_SUBS_SERVER_PATH . '/api/v1/app/' . $FEEL_SUBS_APP_KEY . '/token');
  $result = json_decode($json);
  $feel_subs_token = $result->apptoken;

  // Obtain Feel Apps partner token
  //
  $FEEL_APPS_SERVER_PATH = 'https://feelme.dulta.net';
  $PARTNER_KEY = '<your feel apps api key>'; // Replace with your own Feel Apps partner key
  $string = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/partner/{$PARTNER_KEY}/token");
  $json = json_decode($string, true);
  $feel_apps_token = $json['partner_token'];

  // Obtain mobile app authentication code
  //
  $string = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/user/{$user_id}/auth?partner_token={$feel_apps_token}");
  $json = json_decode($string, true);
  $auth_token = $json['auth_token'];
?><html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <title>FeelApps in background example</title>

</head>
<body>
  <!-- Fixed navbar -->
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">

        <a class="navbar-brand">
          <img src="feel-logo.png" style="display: inline-block; height: 30px; margin-top: -5px"/>
          FeelApps in background example
        </a>
      </div>
    </div>
  </nav>

  <div class="container" role="main" style="padding-top: 60px;">

    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-warning">
          <div class="panel-heading">
            <h3 class="panel-title">Feel app platform connection</h3>
          </div>
          <div class="panel-body">
            <center>
              <div id='connected-div' style='display: none;'>
                <p>Connected to Feel app platform as <strong><?php echo $user_id; ?></strong></p>
                <p>Connected devices: <pre><span id='userdevices'></span></pre></p>
              </div>

              <div id='disconnected-div' style='display: none;'>
                <p>You are not connected to Feel app platform or Feel app is not
                  running. Push the button to connect to the Feel app platform.</p>

                <a class='btn btn-primary btn-lg' id='connect-btn'>Connect</a>
              </div>
            </center>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">Video</h3>
          </div>
          <div class="panel-body">
            <center>
              <video id="video" style="width:600px;max-width:100%;" controls="">
                <source src="http://ftp.nluug.nl/pub/graphics/blender/demo/movies/Sintel.2010.720p.mkv" type="video/mp4">
                Your browser does not support HTML5 video.
              </video>
            </center>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
  <script type="text/javascript" src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="<?php echo $FEEL_APPS_SERVER_PATH; ?>/static/feeljs/1.0/feel.min.js"></script>
  <script>
    // Initialize the JS SDK. Set feel subs application token and feel apps partner key
    var FEEL_SUBS_TOKEN = '<?php echo $feel_subs_token; ?>'
    var FEEL_APPS_TOKEN = '<?php echo $feel_apps_token; ?>'
    var FEEL_APPS_USER_ID = '<?php echo $user_id; ?>'
    $feel.init(FEEL_SUBS_TOKEN, FEEL_APPS_TOKEN, FEEL_APPS_USER_ID)

    // Prepare a link to open mobile app
    $('#connect-btn').attr('href', $feel.apps.getMobileAppLauchUrl('<?php echo $auth_token; ?>'))

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

    var videoId = 'video-id' // Set video id here
    var subtitleId = '0000'                          // Set subtitle id here
    // Load subtitles from the server
    $feel.subs.load(videoId, subtitleId, FEEL_APPS_USER_ID)
      .then(function(){
        console.log('Subtitles loaded')
      }).catch(function(error) {
        console.log('Error loading subtitles: ', error)
      })

    // Handle play/pause events from the video player
    $('#video').on('play', function() {
      var currentTime = this.currentTime
      $feel.subs.play(currentTime)
    }).on('timeupdate', function() {
      var currentTime = this.currentTime
      $feel.subs.timeupdate(currentTime)
    }).on('pause', function() {
      $feel.subs.stop()
    })
  </script>
</body>
</html>
