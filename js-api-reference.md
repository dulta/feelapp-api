# Feel App JavaScript library API reference

## Document version status

This is a DRAFT document, this API is not publically available on production.

## Example

Example code for this SDK can be found at
https://github.com/dulta/feelapp-api/blob/master/examples/slider_example.php

## Initialization

You must include the Feel App JavaScript SDK in your code before initializing the client.

Include Feel App JavaScript SDK:
```
<html>
    <body>
        <script src="https://api.feel-app.com/static/feeljs/1.0/feel.min.js"></script>
    </body>
</html>
```

### Methods

`$feel.apps.init(FEEL_APPS_TOKEN, FEEL_APPS_USER_ID)`

* `FEEL_APPS_TOKEN` - is a Feel App partner token obtained via Feel App REST API call. Please see
  https://github.com/dulta/feelapp-api#get-partner-token for more info
* `FEEL_APPS_USER_ID` - user ID, can be any string. Should uniqly identify the user on your website
  (you can use MD5 hash of your internal user ID, username, etc).

  Please note: in order to connect the mobile app to the JavaScript library the user should scan the QR code
  you obtained Feel App REST API call. Please see
  https://github.com/dulta/feelapp-api#request-user-authorization

# Getting online/offline status and connected devices

`$feel.apps.status.subscribe(callback)`

Subscribe to the online/offline status and connected devices description.
You should provide a `callback` function in the following format:

* `callback: function (userStatus)`

This function fill be called by Feel App JavaScript library each time when user goes online/offline
or list of Bluetooth devices has changed.

`userStatus` will have the following format:

```
userStatus: {
  online: <0 or 1>,
  devices: [{
    name: <device name>,
    id: <device id>,
    motors: [{
      minValue: <min value>,
      maxValue: <max value>,
    }, ...]
  }, ...]
}
```

where

* `device name` is a Bluetooth device name as it's visible to the mobile app user
* `device id` is a unique device id
* `motors` is a list of device motors/servos that can be controlled via API
* `min value` and `max value` are minimum and maximum values that can be sent to the motor

### Example:

```
{
  online: 1,
  deviceDescriptions: [
    {
      "name": "PEARL",
      "id": "8C:DE:52:B1:D1:F1",
      "motors": [
        {
          "minValue": 0,
          "maxValue": 100
        }
      ]
    },
    {
      "name": "ONYX",
      "id": "9C:AE:98:5D:0F:34",
      "motors": [
        {
          "minValue": 0,
          "maxValue": 100
        }
      ]
    }
  ]
]
```

## Subscribe for data coming from devices

`$feel.apps.data.subscribe(callback)`

Subscribe to Bluetooth devices data.

* `callback` is a function called each time when new data is coming from any of connected devices.
  It has following format: `function (value, deviceId)`, where
  * `value` is a device sensor value, integer value in [0..100] range.
  * `deviceId` is the device id (same as you received in `$feel.apps.status.subscribe` callback)

## Sending data to devices

`$feel.apps.data.send(percent, deviceId, motorNumber)`

This function controls device motors.

Parameters:
* `percent` - integer value, corresponds to vibro intensity, rotation speed, etc.
  Should be in `[minValue, maxValue]` range received in `$feel.apps.status.subscribe` callback
* `deviceId` - id of the device. Optional parameter. If omitted, the command is sent to all devices.
* `motorNumber` - 0-base motor number. Optional. If omitted, the command is sent to all motors.

