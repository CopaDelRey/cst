# cst

Please follow these steps to make it work:

1) Create a MySQL database with UTF8 charset
CREATE DATABASE whateverdbname CHARACTER SET utf8 COLLATE utf8_general_ci;

2) Import the database from .sql dump file 'cstest-20190301.sql'.
It is recommended to grant all privileges for the database to a user other that root.

3) Copy all other files into a directory from which it should be served by a web server.
Under Linux, change directory owner recursively to a user which runs a web server:
chown -R user:usergroup /path/to/folder

4) Edit the following 4 lines in the 'config.php' file to setup a database connection:
define('DBSERVERNAME' ... -- database server name, often '127.0.0.1'
define('DBNAME' ... -- database name from step 1
define('DBUSER' ... -- database user, 'root' or a custom user from step 2
define('DBPASS' ... -- password for a database user

5) Try opening the site. In the main menu there are links to both site and admin panel. No user authentication required.
Posting comments on the site is available both for the frontpage and for inner pages.

In the admin panel, in the 'Dashboard' tab you can publish / unpublish any comment.
In the admin panel, in the 'Settings' tab you can setup whether the comments should be posted right away ('Pre-moderate comments'
is off) or be kept unpublished ('Pre-moderate comments' is on) until you publish them manually.
Plus a number of comments per page setup.
