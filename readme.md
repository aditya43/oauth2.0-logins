## About This Project
A most foolproof OAuth 2.0 user authentication system (with refresh tokens) using Guzzle HTTP client.
Supports following OAuth 2.0 login services :
- Google
- Facebook
- Twitter
- Linkedin
- Microsoft
- Yahoo

## How To?
- Clone or download this repo.
- Make sure your dev/prod domain has SSL support.
- Register OAuth applications and use following callback URIs :
```
Google   : https://yourdomain.com/core/adi_callback.php?service=google
Facebook : https://yourdomain.com/core/adi_callback.php?service=facebook
Twitter  : https://yourdomain.com/core/adi_callback.php?service=twitter
Linkedin : https://yourdomain.com/core/adi_callback.php?service=linkedin
Microsoft: https://yourdomain.com/core/adi_callback.php
Yahoo    : https://yourdomain.com/core/adi_callback.php?service=yahoo
```
- Specify OAuth 2.0 client ids and client secrets in `/core/config.php`.
- Specify database connection details in `/core/config.php`.
- Refer to `Database Structure.txt` and run the migration.
- Refer to `/adi_index.php` for sample codes.

## Screenshots

<p align="center">
<img src="https://raw.githubusercontent.com/aditya43/oauth2.0-logins/master/screens/1.jpg" alt="OAuth 2.0 user authentication system by Aditya Hajare.">


<img src="https://raw.githubusercontent.com/aditya43/oauth2.0-logins/master/screens/2.jpg" alt="OAuth 2.0 user authentication system by Aditya Hajare.">


<img src="https://raw.githubusercontent.com/aditya43/oauth2.0-logins/master/screens/3.jpg" alt="OAuth 2.0 user authentication system by Aditya Hajare.">


<img src="https://raw.githubusercontent.com/aditya43/oauth2.0-logins/master/screens/4.jpg" alt="OAuth 2.0 user authentication system by Aditya Hajare.">



<img src="https://raw.githubusercontent.com/aditya43/oauth2.0-logins/master/screens/5.jpg" alt="OAuth 2.0 user authentication system by Aditya Hajare.">



<img src="https://raw.githubusercontent.com/aditya43/oauth2.0-logins/master/screens/6.jpg" alt="OAuth 2.0 user authentication system by Aditya Hajare.">
</p>


## Contact
Comments and feedbacks are welcome. [Drop a line to Aditya Hajare](http://www.adiinviter.com/support) via AdiInviter Pro's support form.

## License
This code is free to use under the terms of [MIT license](http://opensource.org/licenses/MIT).