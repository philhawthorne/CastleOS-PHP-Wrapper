<?php
/**
 * CastleOS Device Class
 * Allows you to take actions against a device easily
 * 
 * @category CastleOS
 * @package  CastleOS
 *
 * @author   Phil Hawthorne
 * @license  GNU
 * @link     http;//philhawthorne.com
 */

require_once "castleosobject.php";

/**
 * CastleOS Device Class
 * Allows you to take actions against a device easily
 * 
 * @category CastleOS
 * @package  CastleOS
 *
 * @author   Phil Hawthorne
 * @license  GNU
 * @link     http;//philhawthorne.com
 */
class CastleOSDevice extends CastleOSObject
{
    public $address;
    public $brightness;
    public $canChangeColor = false;
    public $canChangeColorTemperature = false;
    public $colourTemperatureMax = 6550;
    public $colourTemperatureMin = 2000;
    public $groups = array();
    public $hue;
    public $id;
    public $lastStateChange;
    public $name;
    public $saturation;
    public $status = "off";

    /**
     * Alias of {@see colour()} for those Americans 
     *
     * @param string $color A color to set the light to
     *
     * @return booleab
     * @see colour()
     */
    public function color($color = null)
    {
        return $this->colour($color);
    }
    
    /**
     * Sets the colour for this device
     *
     * @param string $hue        The hue value of the colour you wish to set
     * @param string $saturation The saturation value of the colour you wish to set
     * @param string $brightness The brightness level you wish to set the light to
     *
     * @return boolean
     */
    public function colour($hue = null, $saturation = null, $brightness = null)
    {
        if (empty($this->canChangeColor))
            return false;

        if ($brightness > 1)
            $brightness = $brightness / 100;
        if ($saturation > 1)
            $saturation = $saturation / 100;

        $response = $this->api()->callApi("SetDeviceColorAndBrightness", array("id" => $this->id, "hue" => $hue, "saturation" => $saturation, "brightness" => $brightness));

        if (!empty($response))
            return true;

        return false;
    }

    /**
     * Alias of {@see colourTemperature()} for those Americans
     *
     * @param int $temp The Colour temperature to set the light to
     *
     * @return boolean
     * @see colourTemperature()
     */
    public function colorTemperature($temp)
    {
        return $this->colourTemperature($temp);
    }

    /**
     * Change the colour temperature of a lamp by specifying the Kelvin value
     *
     * @param int $temp The Colour temperature to set the light to
     *
     * @return boolean
     * @see colourTemperature()
     */
    public function colourTemperature($temp)
    {
        if (empty($this->canChangeColorTemperature))
            return false;
        if ($temp < $this->colourTemperatureMin)
            $temp = $this->colourTemperatureMin;
        else if ($temp > $this->colourTemperatureMax)
            $temp = $this->colourTemperatureMax;

        $response = $this->api()->callApi("SetDeviceColorByTemp", array("id" => $this->id, "colorTempKelvin" => $temp));

        exit(print_r($response, true));
        if (!empty($response))
            return true;

        return false;
    }

    /**
     * Dims the light down by 10%, or sets the dim to the specified value
     *
     * @param int $percent Optional. The percentage to dim the device to
     *
     * @return boolean
     */
    public function dim($percent = null)
    {
        if ($percent == null) {
            //Dim the light down by 10%
            $percent = $this->currentState - 10;
        }
        if ($percent <= 0)
            return $this->turnOff();

        //Call the API to set the Dim level
        $response = $this->api()->callApi("SetDeviceDimLevel", array("id" => $this->id, "percent" => $percent));
        if (!empty($response))
            return true;

        return false;
    }

    /**
     * Returns an array of Group IDs that this device belongs to
     *
     * @return array
     */
    public function groups()
    {
        return $this->groups;
    }

    /**
     * Returns the number of groups this device is assigned to
     *
     * @return int
     */
    public function noGroups()
    {
        return count($this->groups);
    }

    /**
     * Sets up a new instance of a CastleOS Device and sets the values for this device
     *
     * @param array         $response An array from the CastleOS API Response
     * @param CastleOS|null &$api     An instance of the CastleOS API we can use to communicate with CastleOS
     *
     * @return CastleOSDevice Returns a new CastleOSDevice object
     */
    public static function setupFromApiResponse(array $response, CastleOS &$api = null)
    {
        $obj = new self;
        if (!empty($api))
            $obj->setApi($api);

        foreach ($response as $param => $val) {
            if (property_exists($obj, $param)) {
                $obj->{$param} = $val;
            }
                
        }

        $groups = array();
        if (!empty($response['groupValue']))
            $groups[] = $response['groupValue'];
        if (!empty($response['groupValue2']))
            $groups[] = $response['groupValue2'];
        if (!empty($response['groupValue3']))
            $groups[] = $response['groupValue3'];

        $obj->groups = $groups;
        
        $obj->id = $response['uniqueId'];
        // if ($obj->id == "66d478e8-89c9-471f-9ba4-c7b8dc04968d")
        //     exit(print_r($response, true)); 

        if ($response['currentState'] > 0)
            $obj->status = "on";


        return $obj;
    }

    /**
     * Tells CastleOS to turn this device off
     *
     * @return boolean Returns true on success, false if something went wrong
     */
    public function turnOff()
    {
        if (empty($this->api))
            return false;

        $response = $this->api()->callApi("ToggleDevicePower", array("id" => $this->id, "power" => "false"));
        if ($response) {
            $this->status = "off";
            return true;
        }
            
        
        return false;
    }

    /**
     * Tells CastleOS to turn this device on
     *
     * @return boolean Returns true on success, false if soemthing went wrong
     */
    public function turnOn()
    {
        if (empty($this->api))
            return false;

        $response = $this->api()->callApi("ToggleDevicePower", array("id" => $this->id, "power" => "true"));
        if ($response) {
            $this->status = "on";
            return true;
        }
            
        
        return false;
    }
}

?>