<?php

class Get4AdsAPI
{
    protected $token;
    protected $testing;
    protected $apiURL = 'https://get4ads.com/api/';

    /**
     * Class constructor
     * 
     * @param string $token
     * @param string $version
     * @return void
     */
    public function __construct( $token = null, $version = '1', $testing = false )
    {
        $this->token = $token;
        $this->apiURL .= 'v' . $version . '/';
        $this->testing = $testing;
    }


    /**
     * Create a new text lead
     * 
     * @param array $data
     * @return object
     */
    public function newTextLead( $data )
    {
        $required = [ 
            'industry-id', 
            'location-id', 
            'customer-name',
            'customer-phone',
            'customer-email'
        ];

        if( !$this->validateRequired( $required, $data ) )
            throw new Exception( 'Required fields are missing' );

        $res = $this->authRequest( 'leads/text', 'POST', $data );

        return $res;
    }


    /**
     * Get all industries
     * 
     * @return object
     */
    public function industries()
    {
        return $this->fetch( 'industries' );
    }


    /**
     * Get all locations
     * 
     * @return object
     */
    public function locations()
    {
        return $this->fetch( 'locations' );
    }


    /**
     * Search for industry
     * 
     * @param string $name
     * @return object
     */
    public function findIndustry( $name )
    {
        return $this->fetch( 'industries/'.urlencode( $name ) );
    }


    /**
     * Search for location
     * 
     * @param string $name
     * @return object
     */
    public function findLocation( $name )
    {
        return $this->fetch( 'locations/'.urlencode( $name ) );
    }


    /**
     * Send unauthenticated request to API
     * 
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return object
     */
    public function fetch( $endpoint, $data = [], $headers = [] )
    {
        // set request headers
        $headers = array_merge([
            'Accept: application/json'
        ], $headers );

        // set request data
        if( $this->testing )
            $data['test'] = true;

        $data = http_build_query( $data );

        // set request URL
        $requestURL = $this->apiURL . $endpoint . '?' . $data;
        
        // init curl instance
        $ch = curl_init();

        curl_setopt_array( $ch, [
            CURLOPT_URL => $requestURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers
        ]);

        // execute curl
        $res = curl_exec( $ch );
        curl_close( $ch );

        // parse JSON response
        return json_decode( $res );
    }


    /**
     * Send authenticated request to API
     * 
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * @param array $data
     * @return object
     */
    private function authRequest( $endpoint, $method = 'POST', $data = [], $headers = [] )
    {
        if( empty( $this->token ) )
            throw new Exception( 'API key is required' );

        // set request method
        $method = strtoupper( $method );

        // set request headers
        $headers = array_merge([
            'Accept: application/json'
        ], $headers );

        // set request data
        $data['api_token'] = $this->token ;

        if( $this->testing )
            $data['test'] = true;
            
        $data = http_build_query( $data );

        // set request URL
        $requestURL = $this->apiURL . $endpoint;
        
        // init curl instance
        $ch = curl_init();

        // set curl options
        $curlOpts = [
            CURLOPT_URL => $requestURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => false
        ];
        
        switch( $method )
        {
            case 'GET':
                $curlOpts[CURLOPT_URL] .= '?'.$data;
                break;

            case 'POST':
                $curlOpts[CURLOPT_POST] = true;
                $curlOpts[CURLOPT_POSTFIELDS] = $data;
                break;

            default:
                $curlOpts[CURLOPT_CUSTOMREQUEST] = $method;
                $curlOpts[CURLOPT_POSTFIELDS] = $data;
                break;
        }

        curl_setopt_array( $ch, $curlOpts );

        // execute curl
        $res = curl_exec( $ch );
        curl_close( $ch );

        // parse JSON response
        return json_decode( $res );
    }


    /**
     * Validate array for required fields
     * 
     * @param array $required
     * @param array $array
     * @return boolean
     */
    private function validateRequired( $required, $array )
    {
        foreach( $required as $key )
            if( !isset( $array[$key] ) )
                return false;

        return true;
    }
}