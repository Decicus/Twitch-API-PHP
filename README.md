Twitch REST API - PHP
==============  

Introduction
--------------
A work in progress PHP project to utilize the Twitch REST API. Documentation for the REST API can be found here: https://github.com/justintv/Twitch-API  
The code itself should be commented enough, but currently there is no dedicated page for documentation. Documentation will be available some time in the future (probably after I "finish" this).  
  
Documentation
--------------
Most of the documentation is in the code itself. However, there is a phpDocumentor page that hasn't been fully "optimized".  
[Documentation page](https://decapi.me/twitchapiphp/classes/TwitchAPI.html)
  
Releases
--------------
I would recommend taking a look at the [releases](https://github.com/Decicus/Twitch-API-PHP/releases), as the ones listed there would be the most stable and fully tested ones.  
While the current, "in-dev" might have more features that would be neat to utilize, they may not be tested and ready for release. Therefore it is recommended to use the latest one from the "Releases" tab.
  
Examples
--------------
### Initialization   
Requires a registered developer application on Twitch: [http://www.twitch.tv/settings/connections](http://www.twitch.tv/settings/connections)  
The three first parameters must be the same as in the developer application settings.  
The fourth parameter is an optional boolean (defaults to true), which enables/disables SSL_VERIFYPEER in the cURL requests. Certain setups (mainly Windows after my tests) do not seem to connect with the API correctly if this is set to true. I recommend attempting to [fix your root certificate bundle](http://snippets.webaware.com.au/howto/stop-turning-off-curlopt_ssl_verifypeer-and-fix-your-php-config/) before using this option.
```
    $TwitchAPI = new TwitchAPI( 'CLIENT_ID', 'CLIENT_SECRET', 'REDIRECT_URL', true );
```  

### Generating authentication URL   
Array with scopes required. List of scopes can be found here under "Scopes": [https://github.com/justintv/Twitch-API/blob/master/authentication.md](https://github.com/justintv/Twitch-API/blob/master/authentication.md)  
If you're unsure what scope you require for your usage, look at the comments/documentation.  
```
    $AuthenticationURL = $TwitchAPI->Authenticate( [ 'user_read', 'chat_login' ] );
```  

### Getting access token 
Twitch API redirects back to the "Redirect URL" set in the application settings and the initialization.  
The authentication code will be available via the code GET variable.
```
    $AccessToken = $TwitchAPI->GetAccessToken( $_GET['code'] );
```  

### Using access token ###
Functions that require the access token will specify it in the comments/documentation.  
Example below only requires the 'user\_read' scope .  
```
    $UserData = $TwitchAPI->GetUserData( $AccessToken );
```  