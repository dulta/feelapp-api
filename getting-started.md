# Getting Started for interactive video

This document will guide you through process of integrating Feel Subtitles and Feel Apps systems.
As a result we are going to get a web page which can play a video in your desktop browser
and send subtitles to the connected
bluetooth device via mobile Feel application.

Result page source code can be found
[here](https://github.com/dulta/feelapp-api/blob/master/examples/subtitles-player-example.php).

### What is it all about?

#### Feel Apps

Feel Apps is a way to connect your webpage to a Bluetooth device via iPhone/Android
mobile application. Your page should have a QR code which is scanned inside the mobile app.
Then the page can send commands to the mobile app which will be transferred to the bluetooth device.

Since the page can be visited by multiple visitors, each visitor should be assigned a unique
user id which is linked to the mobile app during QR code scanning. All commands sent to that user
via Feel Apps system will be redirected to that mobile app instance.

Complete Feel Apps manual can be found [here](https://github.com/dulta/feelapp-api).

#### Feel Subtitles

Feel Subtitles is a database of videos and subtitles for the bluetooth devices.

You as a partner can have multiple videos listed in the database and each video can have
multiple subtitles.

When you show a video on your webpage you can request subtitles from Feel Subtitles
database and play them with JavaScript library we provide.

Complete Feel Subtitles API can be found [here](https://github.com/dulta/feel-subs-api).

### 1. Prerequesites

In order to access Feel Subtitles system you are going to need an _Application Key_.

In order to access Feel Apps system you need a _Partner Key_.

Both Application Key and Partner Key can be obtained from us. Please keep them
in secret and never expose it to user.

### 2. Obtaining Feel Subtitles token

Feel Subtitles Application Key should be exchanged to the _Feel Subtitles Access Token_.
All requests to the Feel Subtitles system should be signed with this token.

This token is valid for 24 hours and can be used on the front-end.

In order to get the token please do the following:

```php
$FEEL_SUBS_SERVER_PATH = 'https://feel-subs.dulta.net';
$FEEL_SUBS_APP_KEY = 'your feel subs app key';
$json = file_get_contents($FEEL_SUBS_SERVER_PATH . '/api/v1/app/' . $FEEL_SUBS_APP_KEY . '/token');
$result = json_decode($json);
$feel_subs_token = $result->apptoken;
```

### 3. Obtaining Feel Apps token

Feel Apps Partner Key should be exchanged to a _Feel Apps Access Token_.
All requests to the Feel Apps system should be signed with this token.

Please note that Feel Apps Access Token is different from Feel Subtitles Access Token.

Feel Apps Access Token is valid for 24 hours and can be used on the front-end.

In order to get the token please do the following:

```php
$FEEL_APPS_SERVER_PATH = 'https://feelme.dulta.net';
$PARTNER_KEY = 'your feel apps partner key';
$string = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/partner/{$PARTNER_KEY}/token");
$json = json_decode($string, true);
$feel_apps_token = $json['partner_token'];
```

### 4. Showing QR code to connect a mobile app

If you want your subtitles to be played on the bluetooth device, you need to connect
the bluetooth device via mobile application. This mobile application can be connected
to your webpage using a QR code which should be scanned inside the mobile app.

To obtain the QR code please do the following:

```php
$string = file_get_contents("{$FEEL_APPS_SERVER_PATH}/api/v1/user/{$user_id}/auth?partner_token={$feel_apps_token}");
$json = json_decode($string, true);
$qrcode = $json['auth_token'];
```

The returned `$qrcode` value is just a sting which can be displayed as a QR code image in many different ways.
In this example we are going to use a 3rd party service:

```html
<img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $qrcode; ?>">
```

Or you could use: 
* http://jeromeetienne.github.io/jquery-qrcode/
* https://github.com/davidshimjs/qrcodejs

### 5. Loading JavaScript library

Subtitles are played by a JavaScript library, you should include it on your web page:

```html
<script src="<?php echo $FEEL_APPS_SERVER_PATH; ?>/static/feeljs/1.0/feel.min.js"></script>
```

You can change `feel.min.js` to `feel.js` if you want to see debug messages in your browser console
but we recommend to use minimized version in production.

### 6. Initializing JavaScript library

You should initialize JavaScript library with Feel Subs token and Feel Apps token before using it:

```JavaScript
var FEEL_SUBS_TOKEN = '<?php echo $feel_subs_token; ?>'
var FEEL_APPS_TOKEN = '<?php echo $feel_apps_token; ?>'
var FEEL_APPS_USER_ID = '<?php echo $user_id; ?>'
$feel.init(FEEL_SUBS_TOKEN, FEEL_APPS_TOKEN, FEEL_APPS_USER_ID)
```

### 7. Loading video subtitles

Before playing subtitles to your bluetooth device you should load them from Feel Subs database.

It can be done with following JavaScript code:

```JavaScript
var videoId = 'video-id' // Set your video id here
var subtitleId = '1234'  // Set subtitle id here

// Load subtitles from the server
$feel.subs.load(videoId, subtitleId, FEEL_APPS_USER_ID)
  .then(function(){
    console.log('Subtitles loaded')
  }).catch(function(error) {
    console.log('Error loading subtitles: ', error)
  })
```

Please make sure you have replaced video id and subtitle id with your own.

Please note that adding videos and subtitles to the Feel Subs database are out of
scope of this manual. For more details please see
[Feel-Subtitles REST API](https://github.com/dulta/feel-subs-api)

### 8. Playing subtitles

You should notify JavaScript library when video is started, paused or rewinded. We also recommend
to do time update calls periodically as the video is being player to keep current video position
in sync with subtitles position.

```JavaScript
$('#video').on('play', function() {
  var currentTimeInSeconds = this.currentTime
  $feel.subs.play(currentTimeInSeconds)
}).on('timeupdate', function() {
  var currentTimeInSeconds = this.currentTime
  $feel.subs.timeupdate(currentTimeInSeconds)
}).on('pause', function() {
  $feel.subs.stop()
})
 ```
