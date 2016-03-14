<?php
/**
 * CastleOS Group Class
 * Allows you to take actions against a group easily
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
 * CastleOS Group Class
 * Allows you to take actions against a group easily
 * 
 * @category CastleOS
 * @package  CastleOS
 *
 * @author   Phil Hawthorne
 * @license  GNU
 * @link     http;//philhawthorne.com
 */
class CastleOSGroup extends CastleOSObject
{
    protected $devices = array();
    public $id;
    public $name;
    public $status = "off";

    /**
     * Returns an array of CastleOSDevice objects that are attached to this group
     *
     * @return array
     */
    public function devices()
    {
        if (empty($this->devices)) {
            $devices = $this->api()->callApi("GetDevicesForGroup", array("groupId" => $this->id));
            if (!empty($devices)) {
                include_once "castleosdevice.php";
                foreach ($devices as $device) {
                    $obj = CastleOSDevice::setupFromApiResponse($device, $this->api);
                    $this->devices[] = $obj;
                }
            }
        }
            
        return $this->devices;
    }

    /**
     * Returns the number of devices in this group
     *
     * @return int
     */
    public function noDevices()
    {
        return count($this->devices);
    }

    /**
     * Sets up a new instance of a CastleOS Group and sets the values for this group
     *
     * @param array         $response An array from the CastleOS API Response
     * @param CastleOS|null &$api     An instance of the CastleOS API we can use to communicate with CastleOS
     *
     * @return CastleOSGroup Returns a new CastleOSGroup object
     */
    public static function setupFromApiResponse(array $response, CastleOS &$api = null)
    {
        $obj = new self;
        if (!empty($api))
            $obj->setApi($api);

        $obj->name = $response['key'];
        $obj->id = $response['value'];

        return $obj;
    }

    /**
     * Tells CastleOS to turn all devices in this group off
     *
     * @return boolean Returns true on success, false if something went wrong
     */
    public function turnOff()
    {
        $response = $this->api()->callApi("ToggleGroupPower", array("groupId" => $this->id, "power" => "false"));
        if ($response)
            return true;
        
        return false;
    }

    
    /**
     * Tells CastleOS to turn all devices in this group on
     *
     * @return boolean Returns true on success, false if soemthing went wrong
     */
    public function turnOn()
    {
        $response = $this->api()->callApi("ToggleGroupPower", array("groupId" => $this->id, "power" => "true"));
        if ($response)
            return true;
        
        return false;
    }
}

?>