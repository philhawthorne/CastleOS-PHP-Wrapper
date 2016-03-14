<?php
/**
 * CastleOS Object Class
 * An abstract class for defining some common functions used by all child classes
 * 
 * @category CastleOS
 * @package  CastleOS
 *
 * @author   Phil Hawthorne
 * @license  GNU
 * @link     http;//philhawthorne.com
 */

abstract class CastleOSObject
{
    protected $api = null; 

    /**
     * Returns the CastleOS for tis object
     *
     * @return CastleOS
     */
    public function api()
    {
        return $this->api;
    }

    /**
     * Sets the API for this object
     *
     * @param CastleOS &$api An instance of CastleOS
     */
    public function setApi(CastleOS &$api)
    {
        $this->api = $api;
    }

    abstract public static function setupFromApiResponse(array $response, CastleOS &$api = null);

    abstract public function turnOff();

    abstract public function turnOn();
}