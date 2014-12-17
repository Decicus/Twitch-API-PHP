<?php
/*
*   Twitch API Class
*/

class TwitchAPI {

    // This shouldn't need to be changed, but it's here just in case the API URL changes for one reason or another.
    var $api_url = 'https://api.twitch.tv/kraken/';

    //Your client ID here:
    var $client_id = '';
    //Your client secret here:
    var $client_secret = '';

    /**
     * Initializes the class with a set redirect URL (from Twitch application settings).
     *
     * @param string $r_url        The redirect URL set in the Twitch application settings.
     */
    var $redirect_url;

    public function __construct( $r_url ) {
        $this->redirect_url = $r_url;
    }
    
    /**
     * Generates an authentication URL with the scope.
     *
     * @param array $scope       The access scope your application will require.
     * @return string        URL to authenticate with.
     */
    public function Authenticate( $scope = [] ) {
        $val = '';
        $n = 0;
        $length = count( $scope );
        foreach( $scope as $s ) {
            if( $n == $length - 1 ) {
                $s .= '';
                $val .= $s;
            }
            else {
                $s .= '+';
                $val .= $s;
            }

            $n++;
        }
        $s = $val;
        $url = $this->api_url . 'oauth2/authorize?response_type=code&client_id=' . $this->client_id . '&redirect_uri=' . $this->redirect_url . '&scope=' . $s;
        return $url;
    }

    /**
     * Retrieves the access token for the user that authenticated.
     *
     * @param string $c       The code passed back to the redirect URL after authenticating with the Twitch API.
     * @return string        Access token to use with Twitch to make requests on the behalf of the user.
     */
    function GetAccessToken( $c ) {
        $curl = curl_init( $this->api_url . 'oauth2/token' );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_POST, 1 );
        $f = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirect_url,
            'code' => $c
        ];
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $f );
        $d = curl_exec( $curl );
        $resp = json_decode( $d, true );
        if( isset( $resp[ 'access_token' ] ) ) {
            return $resp[ 'access_token' ];
        }
        else {
            return false;
        }
    }
    
    /**
     * Gets the display name for the authenticated user using the access token (requires 'user_read' scope).
     *
     * @param string $AT       Access token
     * @return string        Display name (including capitalization).
     */
    function GetDisplayName( $AT ) {
        $curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $this->api_url . 'user' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
		         'Authorization: OAuth ' . $AT
		));
		$o = curl_exec( $curl );
		$resp = json_decode( $o, true );
        curl_close( $curl );

        if( isset( $resp[ 'display_name' ] ) ) {
            return $resp[ 'display_name' ];
        }
        else {
            return false;
        }
    }
    
    /**
     * Gets user data using the access token (requires 'user_read' scope).
     *
     * @param string $AT       Access token
     * @return array        Array with user data
     */
    function GetUserData( $AT ) {

        $curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $this->api_url . 'user' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
		         'Authorization: OAuth ' . $AT
		));
		$o = curl_exec( $curl );
		$resp = json_decode( $o, true );
        curl_close( $curl );

        if( !isset( $resp[ 'error' ] ) ) {
            return $resp;
        }
        else {
            return false;
        }

    }
    
    /**
     * Checks if the user is partnered (to be used with GetUserData()).
     *
     * @param array $array       Array with user data
     * @return boolean       If the user is a partnered streamer or not
     */
    function CheckPartnered( $array ) {

        if( $array[ 'partnered' ] ) {
            return true;
        }
        else {
            return false;
        }

    }
    
    /**
     * Checks if a user is subscribed to a channel or not (requires 'user_subscriptions' scope).
     *
     * @param string $AT       Access token
     * @param string $username      Username of the user you want to verify is subscribed.
     * @param string $channel       Channel of the streamer that the user should be subscribed to.
     * @return boolean        If the user is subscribed or not
     */
    function IsSubscribed( $AT, $username, $channel ) {
        
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $this->api_url . 'users/' . $username . '/subscriptions/' . $channel );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
		         'Authorization: OAuth ' . $AT
		));
        $o = curl_exec( $curl );
        $response = json_decode( $o, true );
        curl_close( $curl );
        
        if( isset( $response['error'] ) ) {
            return false;
        } else {
            return true;
        }
        
    }
    
    /**
     * Gets data for subscribers of a partnered channel with a subscribe button (requires 'channel_subscriptions' scope).
     *
     * @param string $Name       Channel name of partnered streamer.
     * @param string $AT        Access token of channel
     * @param int $count        How many users to retrieve data of.
     * @return array        Array with subscriber data
     */
    function GetSubData( $Name, $AT, $count ) {

        $curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $this->api_url . 'channels/' . $Name . '/subscriptions?limit=' . $count . '&offset=0&direction=desc' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
		        'Authorization: OAuth ' . $AT
		));
		$o = curl_exec( $curl );
		$resp = json_decode( $o, true );
        curl_close( $curl );

        return $resp;

    }
    
    /**
     * Gets videos (broadcasts & highlights) of a channel.
     *
     * @param string $channel       Channel name of streamer you want to retrieve videos from.
     * @param int $limit     How many videos you want to retrieve.
     * @param int $offset        Object offset for pagination.
     * @param boolean $broadcasts      If you want to retrieve both broadcasts and highlights, or only highlights (defaults to only highlights).
     * @return array        Array with video data.
     */
    function FetchVideos( $channel, $limit = 10, $offset = 0, $broadcasts = false ){
    
        $curl = curl_init();
        $B = $broadcasts ? '&broadcasts=true' : '';
        curl_setopt( $curl, CURLOPT_URL, $this->api_url . '/channels' . $channel . '/videos?limit=' . $limit . '&offset=' . $offset . $B );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        $o = curl_exec( $curl );
        $resp = json_decode( $o, true );
        curl_close( $curl );

        return $resp;
        
    }
    
    /**
     * Gets stream data for a channel.
     *
     * @param string $channel       Channel name of streamer you want to retrieve channel data for.
     * @return array        Array with channel data.
     */
    function Streams( $channel ) {
    
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $this->api_url . '/streams/' . $channel );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        $o = curl_exec( $curl );
        $resp = json_decode( $o, true );
        curl_close( $curl );
        
        return $resp;
        
    }
    
    /**
     * Gets list of blocked users from an authenticated user (requires 'user_blocks_read' scope).
     *
     * @param string $AT        Access token.
     * @param string $user      Username of authenticated user.
     * @param int $limit        Limit of how many user objects to retrieve (default: 25).
     * @param int $offset       Object offset (default: 0).
     * @return array
     */
    function Blocks( $AT, $user, $limit = 25, $offset = 0 ) {
    
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $this->api_url . '/users' . $user . '/blocks?limit=' . $limit . '&offset=' . $offset );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
		        'Authorization: OAuth ' . $AT
		));
        $o = curl_exec( $curl );
        $resp = json_decode( $o, true );
        curl_close( $curl );

        return $resp;
        
    }
}

?>
