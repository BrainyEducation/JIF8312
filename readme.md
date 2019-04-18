# Version 1.0 Release Notes
## What’s New
1. We have added a search bar throughout the website so that users may be able to search for stories based on title, author or keyword. 
2. Users can filter their search results based on language and section (Children’s and/or Adults).
3. Users who click on the Think and Do’s star icon next to a story will be redirected to a list of all the Think and Do’s related to that specific story.
## Known Bugs
Some keywords do not return any search results and do not redirect to the default ‘no results’ page. This includes queries with special accents in the search.

# Install Guide
## Prerequisites & Required Software
1. XAMPP (available here: https://www.apachefriends.org/index.html)
2. Code repository (available here: https://github.com/ldouglas7/JIF8312)
3. Hearatale database (available here: https://docs.google.com/spreadsheets/d/1AsdrAJfTxF1KrlgbgIhrL0bQlBSItqTPa0mfptdoS1A/edit?usp=sharing) 

# Setup Instructions
## XAMPP: 
1. Download the latest version of XAMPP for the appropriate operating system.
2. Click on the XAMPP application
3. Click Manage Servers
4. Start MySQL Database
5. Start Apache Web Server
## Code repository:
1. Click the ‘Clone or Download’ button > Download ZIP
2. Open the zip file and move the folder into Applications/XAMPP/htdocs
## Hearatale database:
1. Click File > Download as > Comma-separated values
2. Go into your browser and go to localhost/
3. Click phpmyadmin in the top right corner
4. Click on test > import > choose file
5. Select the downloaded CSV file
6. Scroll down and select the checkbox: ‘The first line of the file contains the table column names’
7. Press Go
8. A new table should have been created 
9. Click the Operations tab
10. Rename database to ‘updated_hearatale’
11. Press Go
12. Click the Structure tab
13. Click the checkbox: ‘Check all’
14. Click Change
15. Change all the columns to type TEXT
16. Click Save

# Run Instructions
1. Go into your browser and go to localhost/JIF8312-master/
2. Click on home.html

# Troubleshooting
## The MySQL database will not start
1. Click on MySQL and press configure
2. Change the port number to 3307
3. Press start 

