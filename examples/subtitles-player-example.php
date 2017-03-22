<?php
  $user_id = $_GET['userid'];
  if (!$user_id) {
    header('Location: ' . $_SERVER["PHP_SELF"] . '?userid=' . rand(1111, 9999));
    die();
  }

  // Obtain Feel Subs token
  //
  $FEEL_SUBS_SERVER_PATH = 'https://api.pibds.com';
  $FEEL_SUBS_APP_KEY = 'your feel subs app key';
  $json = file_get_contents($FEEL_SUBS_SERVER_PATH . '/api/v1/app/' . $FEEL_SUBS_APP_KEY . '/token');
  $result = json_decode($json);
  $feel_subs_token = $result->apptoken;

  // Obtain Feel Apps partner token
  //
  $FEEL_APPS_SERVER_PATH = 'https://api.feel-app.com';
  $PARTNER_KEY = 'your feel apps partner key';
  $string = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/partner/{$PARTNER_KEY}/token?user=" . urlencode($user_id));
  $json = json_decode($string, true);
  $feel_apps_token = $json['partner_token'];

  // Obtain QR code
  //
  $string = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/user/{$user_id}/auth?partner_token={$feel_apps_token}");
  $json = json_decode($string, true);
  $qrcode = $json['auth_token'];

  // Getting user status (online/offline, etc)
  $userstatus = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/user/{$user_id}/status?partner_token={$feel_apps_token}");

?><html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

</head>
<body>
  <!-- Fixed navbar -->
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">

        <a class="navbar-brand">
          <img src="feel-logo.png" style="display: inline-block; height: 30px; margin-top: -5px"/>
          FeelApps Subtitles Player example
        </a>
      </div>
    </div>
  </nav>

  <div class="container" role="main" style="padding-top: 60px;">

    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-success">
          <div class="panel-heading">
            <h3 class="panel-title">User</h3>
          </div>
          <div class="panel-body">
            <h4>User ID</h4>
            <pre><?php echo $user_id; ?></pre>
            <h4>User status</h4>
            <pre><?php echo $userstatus; ?></pre>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">QR code to scan</h3>
          </div>
          <div class="panel-body">
            <center>
              <p><img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $qrcode; ?>"></p>
              <pre><?php echo $qrcode; ?></pre>
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

    var videoId = 'video-id' // Set your video id here
    var subtitleId = '1234'  // Set subtitle id here

    // Load subtitles from the server
    $feel.subs.load(videoId, subtitleId, FEEL_APPS_USER_ID)
      .then(function(){
        console.log('Subtitles loaded')
      }).catch(function(error) {
        console.log('Error loading subtitles: ', error)
      })

    // Handle play/pause events from the video player
    $('#video').on('play', function() {
      var currentTimeInSeconds = this.currentTime
      $feel.subs.play(currentTimeInSeconds)
    }).on('timeupdate', function() {
      var currentTimeInSeconds = this.currentTime
      $feel.subs.timeupdate(currentTimeInSeconds)
    }).on('pause', function() {
      $feel.subs.stop()
    })
  </script>
</body>
</html>
