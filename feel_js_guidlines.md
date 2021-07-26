# Feel Javascript library (1.0) usage guidlines

## In desktop browsers

In order to play subtitled videos in the desktop browsers please do the following:
- Server-side:
  - Obtain Feel App partner token via REST API using your Feel App partner key:
    More details:
      - https://github.com/dulta/feelapp-api#get-partner-token
      - https://github.com/dulta/feelapp-api/blob/master/getting-started.md#3-obtaining-feel-apps-token
  - Obtain Feel Subtitles application token via REST API using your Feel Subtitles application key:
    More details:
      - https://github.com/dulta/feel-subs-api#get-application-token
      - https://github.com/dulta/feelapp-api/blob/master/getting-started.md#2-obtaining-feel-subtitles-token
  - If this is a known user, then you can check if he alreay has the Feel Connect app installed and
    scanned the QR code and if the Feel Connect mobile app is online:
    https://github.com/dulta/feelapp-api/blob/master/README.md#get-user-status

- Client-side:
  On the video page:
  - Include Feel Javascript library on your page
  - Show a button to play video interactively
  - When user clicked the button:
    - Initialize the library with tokens and video ID
      More details:
        - https://github.com/dulta/feelapp-api/blob/master/getting-started.md#6-initializing-javascript-library

    - Optionally subscribe for user status updates:

      https://github.com/dulta/feelapp-api/blob/master/examples/slider_example.php#L101

      In this case you can track if user has Feel Connect app running and connected and notify the user
      if connection is lost.

    - If the Feel Connect mobile app is not online:
      - provide a link to Feel Connect app in app stores:
        - https://play.google.com/store/apps/details?id=net.dulta.feelclient
        - https://itunes.apple.com/us/app/feel-connect/id1119170156
      - Show the QR code to scan with Feel Connect app:
        - https://github.com/dulta/feelapp-api/blob/master/getting-started.md#4-showing-qr-code-to-connect-a-mobile-app

      Please note that if this user has scanned the QR code before, then running the Feel Connect app make it
      online immediately so the users doesn't have to scan it again.

    - Load the subtitles:
      https://github.com/dulta/feelapp-api/blob/master/getting-started.md#7-loading-video-subtitles

### Security considerations

#### Handling secret keys

Please keep your Feel Subtitles App Key and Feel Apps Partner Key secret and never reveal
them on the client side.

#### Feel App partner token

When requesting Feel App partner token via REST APIs, please always provide the user ID with this API call:
  - https://github.com/dulta/feelapp-api#get-partner-token

If you do not provide the user ID, then the returned token will allow to get access to any
user realtime connection and online status which can be considered as a security problem.

#### Feel Subtitles application token

When requesting Feel Subtitles application token via REST APIs, please always provide the video ID with
this API call:
  - https://github.com/dulta/feel-subs-api#get-application-token

If you do not provide the video ID, then the returned token will allow to get access to any
of your video subtitles which can be considered as a security problem.

### Library location

Library v1.1 is located at
`https://api.feel-app.com/static/feeljs/1.1/feel.min.js`

The library should be included on the desktop (not mobile) versions of your video pages.

You can cache the library or keep it on your CDN server. In this case please let us know
so we could notify you when we release a new version.

### Library initialization

You should call `$feel.init` in order to initialize the library and establish connection
between your video and connected Feel Connect app.

Please do not do it for all visitors, do it only as soon as user clicked
the button to play the video interactively. This function establishes connection to Feel
servers and we would like to avoid unnesessary connections.

### Choosing user ID

You can use any string up to 1000 characters long as a user ID. This ID should be unique
for all your users.

If the user is anonimous, you can use your session ID as a user ID. In this case the user
will have to scan a new QR code each time a new session is started.

As an example you can use following as user ID:
  - a hashed or non-hashed value of your database user ID,
  - a hashed or non-hashed username,
  - a hashed or non-hashed user e-mail address.

Please be aware that non-hashed values reveal user details (your internal database user ID
or e-mail address for example) to us and to end-users.

Please note that user ID will be visible to users inside Feel Connect app as
`Connected user id: <userid>` message.

We recommend to use only url-safe characters in your user IDs (uppercase and lowercase letters,
decimal digits, hyphen, period, underscore, and tilde), otherwise you will have to
urlencode them in REST API calls.

### Choosing video ID

You can use any string up to 1000 characters long as a video ID. This ID should be unique
for all your videos.

Prior to using our system, we should upload subtitles for your videos to our servers.
In order to do that you should send us a spreadsheet with your videos and specify which
video ID you want to use for each of them.

We recommend to use only url-safe characters in your user IDs (uppercase and lowercase letters,
decimal digits, hyphen, period, underscore, and tilde), otherwise you will have to
urlencode them in REST API calls.

### QR code generation

We do not recommend to use 3-rd party servers (like api.qrserver.com) to generate QR codes on production,
because this server outage will affect your customers' ability to connect devices to the video.

Instead you can use JavaScript libraries which generate QR code images out of strings on the client side:
  - http://jeromeetienne.github.io/jquery-qrcode/
  - https://github.com/davidshimjs/qrcodejs

## In mobile browsers

In order to play the video on the mobile:
- Server side:
  - Obtain Feel Subtitles application token via REST API
    More details:
      - https://github.com/dulta/feel-subs-api#get-application-token
      - https://github.com/dulta/feelapp-api/blob/master/getting-started.md#2-obtaining-feel-subtitles-token
- Client side:
  - On your video page provide a link to play the video:

    `feelapp://video?url=<video-stream-url>&video-id=<video-id>&token=<feel-subtitles-application-token>`

    where
    - `<video-stream-url>` - direct link to your video stream
    - `<video-id>` - video ID in our subtitles database
    - `<feel-subtitles-application-token>` - token obtained on the server side

    Clicking this link will launch Feel Connect app where user will be able to connect bluetooth device
    and start playing the video.

  - Please also privide a link to the Feel Connect app in the app stores:
    - https://play.google.com/store/apps/details?id=net.dulta.feelclient
    - https://itunes.apple.com/us/app/feel-connect/id1119170156
