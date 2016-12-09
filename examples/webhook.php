<?php
  $PARTNER_KEY = '<PUT YOUR PARTNER KEY HERE>';
   
  $SERVER_URL = 'https://api.feel-app.com';
  $API_BASE = $SERVER_URL . '/api/v1';
  $servier_response = '';

  if ($_POST['webhook']) {
    $curl = curl_init("{$API_BASE}/partner/webhook");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
      'url' => $_POST['webhook_url'],
      'key' => $PARTNER_KEY
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);

    $servier_response = "Webhook user status URL changed, server response: $response";
  }

  $string = file_get_contents("{$API_BASE}/partner/webhook?key={$PARTNER_KEY}");
  $json = json_decode($string, true);
  $url = $json['url'];

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
        <div class="col-md-8 col-md-offset-2 col-sm-12">


          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Webhooks</h3>
            </div>
            <div class="panel-body">
              <form method="post">
                <div class="form-group">
                  <label for="webhook_url">Webhook URL (string)</label>
                  <input type="text" id="webhook_url" name="webhook_url" class="form-control"
                    value="<?php echo $url; ?>"/>
                </div>

                <input type="hidden" name="webhook" value="1"/>
                <input type="submit" value="Update webhook URL" class="btn btn-lg btn-default"/>
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
