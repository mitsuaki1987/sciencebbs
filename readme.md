ScienceBBS
==========

BBS for scientific discussion. It has the following features:

* Tex formula
* Free-hand schematic illustration
* Display JPEG, PNG, GIF, MPEG, etc.
* Attach files such as PDF.
* Running with PHP

There are following two type of BBS

* Without authentification.
* With Basic authentification

Without authentification
------------------------

The usage is just to copy `src/noauth/index.php` into any HTTP/HTTPS server.

With Basic authentification
---------------------------

The usage is as follows:

1. copy `src/auth/index.php` into any HTTP/HTTPS server.
2. Create `.htaccess` in the same directory. Its contents is as follows
   ```
   AuthUserFile DIRECTORY_NAME/.htpasswd
   AuthName projectname
   AuthType Basic
   <Limit GET>
   require valid-user
   </Limit>
   ```
   where *DIRECTORY_NAME* is the full-path to the directory where `index.php` locates.
3. Create password file and add first user with the following command:
   ``` bash
   htpasswd -cm DIRECTORY_NAME/.htpasswd USER_NAME1
   chgrp GROUP-NAME DIRECTORY_NAME/.htpasswd
   chmod o-w DIRECTORY_NAME/.htpasswd
   ```
   whrere *USER_NAME1* is the name of the first user, *GROUP-NAME* is the appropriate owner group in that server.
4. Add other users (*USER_NAME2*) as follws:
   ``` bash
   htpasswd -m DIRECTORY_NAME/.htpasswd USER_NAME2
   ```
5. The previous command is also used to change the password for any user (*USER_NAME*).
   ``` bash
   htpasswd -m DIRECTORY_NAME/.htpasswd USER_NAME
   ```
6. To remove authentification for any user (*USER_NAME*), please type
   ``` bash
   htpasswd -D DIRECTORY_NAME/.htpasswd USER_NAME
   ```
