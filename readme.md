# Version 1.0 Release Notes
What’s New
We have added a search bar throughout the website so that users may be able to search for stories based on title, author or keyword. 
Users can filter their search results based on language and section (Children’s and/or Adults).
Users who click on the Think and Do’s star icon next to a story will be redirected to a list of all the Think and Do’s related to that specific story.
Known Bugs
Some keywords do not return any search results and do not redirect to the default ‘no results’ page. This includes queries with special accents in the search.
Install Guide
Prerequisites & Required Software
XAMPP (available here: https://www.apachefriends.org/index.html)
Code repository (available here: https://github.com/ldouglas7/JIF8312)
Hearatale database (available here: https://docs.google.com/spreadsheets/d/1AsdrAJfTxF1KrlgbgIhrL0bQlBSItqTPa0mfptdoS1A/edit?usp=sharing) 
Setup Instructions
XAMPP: 
Download the latest version of XAMPP for the appropriate operating system.
Click on the XAMPP application
Click Manage Servers
Start MySQL Database
Start Apache Web Server
Code repository:
Click the ‘Clone or Download’ button > Download ZIP
Open the zip file and move the folder into Applications/XAMPP/htdocs
Hearatale database:
Click File > Download as > Comma-separated values

Go into your browser and go to localhost/

Click phpmyadmin in the top right corner
Click on test > import > choose file

Select the downloaded CSV file
Scroll down and select the checkbox: ‘The first line of the file contains the table column names’
Press Go
A new table should have been created 
Click the Operations tab
Rename database to ‘updated_hearatale’
Press Go
Click the Structure tab
Click the checkbox: ‘Check all’

Click Change
Change all the columns to type TEXT

Click Save
Run Instructions
Go into your browser and go to localhost/JIF8312-master/

Click on home.html


Troubleshooting
The MySQL database will not start
Click on MySQL and press configure
Change the port number to 3307
Press start 

