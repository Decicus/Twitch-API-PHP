Twitch REST API - PHP
==============  

Introduction
--------------
A work in progress PHP project to utilize the Twitch REST API. Documentation for the REST API can be found here: https://github.com/justintv/Twitch-API  
The code itself should be commented enough, but currently there is no dedicated page for documentation. Documentation will be available some time in the future (probably after I "finish" this).  
  
Documentation
--------------
No dedicated documentation page has been setup yet. However, there are comments in the code itself, which should guide you in the right direction.  
**Documentation page ETA: _Soon™_**
  
Examples
--------------
### Initialization ###  
Requires a registered developer application on Twitch: [http://www.twitch.tv/settings/connections](http://www.twitch.tv/settings/connections)  
All parameters must be the same as in the developer application settings.  
```
    $TwitchAPI = new TwitchAPI( 'CLIENT_ID', 'CLIENT_SECRET', 'REDIRECT_URL' );
```  

### Generating authentication URL ###  
Array with scopes required. List of scopes can be found here under "Scopes": [https://github.com/justintv/Twitch-API/blob/master/authentication.md](https://github.com/justintv/Twitch-API/blob/master/authentication.md)  
If you're unsure what scope you require for your usage, look at the comments/documentation.  
```
    $AuthenticationURL = $TwitchAPI->Authenticate( [ 'user_read', 'chat_login' ] );
```  

### Getting access token ###
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