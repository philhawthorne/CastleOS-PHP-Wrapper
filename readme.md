# CastleOS PHP Wrapper
A PHP library to help you communicate with the home automation sofrware CastleOS over their HTTP API.

**Author:** [Phil Hawthorne](http://philhawthorne.com)

**License:** GNU

**Date:** March 2016

### How to use this class
You'll first need to make sure you're running [CastleOS](http://castleos.com), and its accessible over your network.

Once you have CastleOS up and running, include this class in your project like so

```php
    <?php
    require_once "castleos.php";
    ?>
```

You'll then need to set your CastleOS username, password and hostname when calling the class.

```php
    <?php
    $settings = array("username" => "admin", "password" => "password1234", "hostname" => "192.168.1.x"(;
    $castleos = new CastleOS($settings);
    ?>
```
    
##### A note on users and passwords

CastleOS requires you to create your own user account. **Don't use the default admin user**. 

When communicating with the CastleOS API, a token is used to authenticate you. This token expires once every 90 days, unless your username contains *kinect*. This class will authenticate your username and password on each call to your system, and cache the token for that execution.

This may increase latency when making several HTTP requests with this class. If you prefer, you can cache your access token locally, and parse it to this class in the settings array like so:

```php
    <?php
    $settings = array("username" => "admin", "token" => "zzzzzjkffhsbf...", "hostname" => "192.168.1.x");
    $castleos = new CastleOS($settings);
    ?>
```

If your token expires, you'll need to generate a new one.

## Getting Devices, Scenes and Groups

To get an array of all devices available on CastleoS, use the following:
    
```php
    <?php
    $devices = $castleos->getDevices();
    $scenes = $castleos->getScenes();
    $groups = $cstleos->getGroups();
    ?>
```

## Turning on/off Devices, Scenes and Groups

There are common `turnOn()` and `turnOff()` methods available on all Devices, Scenes and Groups. A call to these metthods will take action on CastleOS.

```php
    <?php
    $castleos->device("fgghj-3hngb4-bgjdb-fngbrb4-fjght")->turnOn();
    $castleos->group(4)->turnOn();
    $castleos->scene(10)->turnOff();
    ?>
```
    
## Device Status
You can see the power status of a device by checking

```php
    <?php
    $castleos->device("fgghj-3hngb4-bgjdb-fngbrb4-fjght")->status; //Either off or on
    ?>
```

## Dimming and Changing Colours
Some devices such as Hue bulbs or Zwave dimmer switches allow you to control their colour or dim level. This class supports updating those as well.

### Dimming a Bulb/Device

```php
    <?php
    $castleos->device("fgghj-3hngb4-bgjdb-fngbrb4-fjght")->dim(); //Dim by 10%
    $castleos->device("fgghj-3hngb4-bgjdb-fngbrb4-fjght")->dim(50); //Set dim to 50%
    $castleos->device("fgghj-3hngb4-bgjdb-fngbrb4-fjght")->dim(100) //Set to full brightness;
    ?>
```

### Changing Colours
Colours can be set by specifying either a HSB value, or Kelvin Temperature.

```php
    <?php
    $castleos->device("fgghj-3hngb4-bgjdb-fngbrb4-fjght")->colour(360, 100, 100); 
    $castleos->device("fgghj-3hngb4-bgjdb-fngbrb4-fjght")->colourTemperature(2700); 
    ?>
``` 
    
Not all devices support changing colours by temperature. If a device does not support changing colour by temperature, your call to `colourTemperature()` will return `false`.
