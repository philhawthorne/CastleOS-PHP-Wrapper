<?php
/**
 * CastleOS Scene Class
 * Allows you to take actions against a scene easily
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
 * CastleOS Scene Class
 * Allows you to take actions against a scene easily
 * 
 * @category CastleOS
 * @package  CastleOS
 *
 * @author   Phil Hawthorne
 * @license  GNU
 * @link     http;//philhawthorne.com
 */
class CastleOSScene extends CastleOSObject
{
    public $devices = array();
    public $groups = array();
    public $id;
    public $name;
    public $scenes = array();
    public $scripts = array();
    public $status = "off";

    /**
     * Returns the decices array, which contains their IDs and settings for this scene
     *
     * @return array Returns an array
     */
    public function devices()
    {
        return $this->devices;
    }

    /**
     * Returns an array of groups and their status while in this scene
     *
     * @return array
     */
    public function groups()
    {
        return $this->groups;
    }

    /**
     * Returns the number of devices in this scene
     *
     * @return int
     */
    public function noDevices()
    {
        return count($this->devices);
    }

    /**
     * Returns the number of groups in this scene
     *
     * @return int
     */
    public function noGroups()
    {
        return count($this->groups);
    }

    /**
     * Returns the number of scenes in this scene
     *
     * @return int
     */
    public function noScenes()
    {
        return count($this->scenes);
    }

    /**
     * Returns the number of scripts that are in this scene
     *
     * @return int
     */
    public function noScripts()
    {
        return count($this->scripts);
    }

    /**
     * Sets up a new instance of a CastleOSScene and sets the values for this scene
     *
     * @param array         $response An array from the CastleOS API Response
     * @param CastleOS|null &$api     An instance of the CastleOS API we can use to communicate with CastleOS
     *
     * @return CastleOSScene Returns a new CastleOSScene object
     */
    public static function setupFromApiResponse(array $response, CastleOS &$api = null)
    {
        $obj = new self;
        if (!empty($api))
            $obj->setApi($api);

        if (!empty($response['devicesInScene']))
            $obj->devices = $response['devicesInScene'];
        if (!empty($response['groupsInScene']))
            $obj->groups = $response['groupsInScene'];
        if (!empty($response['scenesInScene']))
            $obj->scenes = $response['scenesInScene'];
        if (!empty($response['scriptsInScene']))
            $obj->scripts = $response['scriptsInScene'];

        $obj->name = $response['sceneName'];
        $obj->id = $response['sceneValue'];


        return $obj;
    }

    /**
     * Returns an array of scenes and their settings while in this scene
     *
     * @return array
     */
    public function scenes()
    {
        return $this->scenes;
    }

    /**
     * Tells CastleOS to turn all devices in this scene off
     *
     * @return boolean Returns true on success, false if something went wrong
     */
    public function turnOff()
    {
        if (empty($this->api))
            return false;

        $response = $this->api()->callApi("ToggleScenePower", array("sceneId" => $this->id, "power" => "false"));
        if ($response)
            return true;
        
        return false;
    }

    /**
     * Tells CastleOS to turn all devices in this scene on
     *
     * @return boolean Returns true on success, false if soemthing went wrong
     */
    public function turnOn()
    {
        if (empty($this->api))
            return false;

        $response = $this->api()->callApi("ToggleScenePower", array("sceneId" => $this->id, "power" => "true"));
        if ($response)
            return true;
        
        return false;
    }
}

?>