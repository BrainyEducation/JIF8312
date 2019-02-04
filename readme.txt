HOW TO SET UP A TEST SERVER (on windows)

1 // Download WAMPServer

2 // Ensure that all of the WAMP services are running

3 // Place the site directory into C://wamp64/www/

4 // Type localhost into the address bar of your web browser

5 // Select the name of the site's directory that you placed in the www folder

HOW TO SET UP THE SERVER (on mac)

1 // Download XAMPP

2 // Click on 'Manage Servers' and check that you can run the Apache Web Server and the MySQL Database

2b // If you cannot run the MySQL Database, click on it and press reconfigure -> change the port number to 3308

3 // Place the site repository into Applications > XAMPP > htdocs

4 // Type localhost/JIF8312/ into the address bar of your web browser

5 // The HTML file should be loaded and if you set up the database, then everything should be working


HOW TO SET UP A DATABASE

1 // From the WAMP application, open PHPMyAdmin

2 // If prompted, enter 'root' for the username and leave the password field blank

3 // If one doesn't already exist, create a database called 'test' and a table inside of it called 'updated_hearatale'

4 // This table should include, at the very least, a column for each attribute that appears in the $query line of the search file (Name, Category, etc). Every field should be of type TEXT

5 // Now, add values to the table using SQL's INSERT function. Information on how to do this can be found on the web
