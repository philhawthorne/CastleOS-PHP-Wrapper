<?php
/**
 * CastleOS API PHP Wrapper
 * Allows you to communicate with the CastleOS API
 *
 * @category CastleOS
 * @package  CastleOS
 *
 * @author   Phil Hawthorne
 * @license  GNU
 * @link     http;//philhawthorne.com
 */
class CastleOS
{
    protected $host = "localhost";
    protected $password;
    protected $token;
    protected $username;
    

    /**
     * Sets up this class with the settings array you provide
     *
     * @param array $settings An array of settings to be used for this class
     */
    public function __construct(array $settings)
    {
        if (!empty($settings['username']))
            $this->setUsername($settings['username']);

        if (!empty($settings['password']))
            $this->setPassword($settings['password']);

        if (!empty($settings['token']))
            $this->setToken($settings['token']);

        if (!empty($settings['host']))
            $this->setHost($settings['host']);
    }

    /**
     * Calls the CastleOS API via HTTP
     *
     * @param string $url    The URL endpoint to call
     * @param array  $data   An array of data to send
     * @param string $method Optional. The HTTP request type. Only GET is currently supported
     *
     * @return array
     */
    public function callApi($url, array $data = array(), $method = "get")
    {
        switch ($method) {
            default:
                if (!empty($data)) {
                    $first = true;
                    foreach ($data as $param => $value) {
                        $url .= $first ? "?" : "&";
                        $url .= "{$param}=".urlencode($value);
                        $first = false;
                    }
                }
            break;
        }
    
        $api_url = "http://{$this->host}/CastleOS/service/web/{$url}";
        /*if (!$this->hasToken())
            $this->getToken();
        if (!$this->hasToken())
            return array();*/
        
        $headers = array("X-CastleOS-Authorization: {$this->generateAuthorization()}");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);
        curl_close($ch);
        
        if (empty($body)) {
            return array();
        }

        $content = json_decode($body, true);

        return $content;
    }

    /**
     * Returns a CstleOSDevice object for the ID specified
     *
     * @param string $id The CstleOS Unique device ID
     *
     * @return CstleOSDevice
     */
    public function device($id)
    {
        $allDevices = $this->getDevices();
        if (empty($allDevices))
            return false;

        foreach ($allDevices as $device)
            if ($device->id == $id)
                return $device;
        
        return false;
    }

    /**
     * Generates an authorization header that CastleOS will use to authenticate this request
     *
     * @return string Returns the auithorization string
     * @access protected
     */
    protected function generateAuthorization()
    {
        return $this->username . ":" . $this->token;
    }

    /**
     * Retuens an array of CastleOSDevices objects
     *
     * @return array
     */
    public function getDevices()
    {
        include_once "objects/castleosdevice.php";
        
        $response = $this->callApi("GetDevices");
        if (empty($response))
            return array();

        $return = array();
        foreach ($response as $device) {
            $device = CastleOSDevice::setupFromApiResponse($device, $this);
            if (!empty($device)) {
                $return[] = $device;
            }                
        }

        return $return;
    }

    /**
     * Returns an array of Groups from CastleOS
     *
     * @return array Returns an array
     */
    public function getGroups()
    {
        include_once "objects/castleosgroup.php";
        
        $response = $this->callApi("GetAllGroups");
        if (empty($response))
            return array();

        $return = array();
        foreach ($response as $group) {
            $group = CastleOSGroup::setupFromApiResponse($group, $this);
            if (!empty($group)) {
                $return[] = $group;
            }                
        }

        return $return;
    }

    /**
     * Returns an array of Scenes from CastleOS
     *
     * @return array Returns an array
     */
    public function getScenes()
    {
        include_once "objects/castleosscene.php";

        $response = $this->callApi("GetAllScenes");
        if (empty($response))
            return array();

        $return = array();
        foreach ($response as $scene) {
            $scene = CastleOSScene::setupFromApiResponse($scene, $this);
            if (!empty($scene)) {
                $return[] = $scene;
            }                
        }

        return $return;
    }

    /**
     * Uses the username and password stored in this instance to generate a CastleOS access token
     *
     * @return string Returns the access token, or false if something went wrong
     */
    public function getToken()
    {
        $username = urlencode($this->username);
        $password = urlencode($this->password);
        $api_url = "http://{$this->host}/CastleOS/service/web/AuthenticateUser_PlainText?username={$username}&password={$password}";
        
        $ch = curl_init();
        $headers = array('Accept: application/json');
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36");
        curl_close($ch);

        if (empty($body)) {
            return false;
        }
        
        $content = json_decode($body, true);
        if (empty($content) || empty($content['securityToken']))
            return false;

        $this->token = $content['securityToken'];

        return $this->token;
    }

    /**
     * Returns a CastleOSGroup object for the Group ID specified
     *
     * @param string $id The CastleOS Group ID
     *
     * @return CastleOSGroup
     */
    public function group($id)
    {
        $allGroups = $this->getGroups();
        if (empty($allGroups))
            return false;

        foreach ($allGroups as $group)
            if ($group->id == $id)
                return $group;

        return false;
    }

    /**
     * Returns true if this instance has a token cached for calls to the API
     *
     * @return boolean Returns true or false
     */
    public function hasToken()
    {
        return !empty($this->token);
    }

    /**
     * Sets the host used for API calls to this class
     *
     * @param string $host The host. If none specified, localhost is assumed
     *
     * @return CastleOs Returns itself for method chaining
     */
    public function setHost($host = "localhost")
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Sets the password used for API calls to this class
     *
     * @param string $password The password.
     *
     * @return CastleOs Returns itself for method chaining
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Sets the token used for API calls to this class
     *
     * @param string $token The token.
     *
     * @return CastleOs Returns itself for method chaining
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Sets the username used for API calls to this class
     *
     * @param string $username The username
     *
     * @return CastleOs Returns itself for method chaining
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Returns a CasstleOS Scene by its ID
     *
     * @param int $id The CastleOS Scene ID
     *
     * @return CastleOSScene
     */
    public function scene($id)
    {
        $allScenes = $this->getScenes();
        if (empty($allScenes))
            return false;

        foreach ($allScenes as $scene)
            if ($scene->id == $id)
                return $scene;

        return false;
    }
}

?>