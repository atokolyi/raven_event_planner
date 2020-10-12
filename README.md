# Raven event planner

Allows Cambridge University JCRs/MCRs/clubs to deploy a raven-authenticated online platform where they can create and join events around Cambridge while following current [NHS guidelines](https://www.gov.uk/government/publications/coronavirus-covid-19-meeting-with-others-safely-social-distancing/coronavirus-covid-19-meeting-with-others-safely-social-distancing).



### Initial setup

1. Clone the repository on to your web host (i.e. the fantastic Cambridge [SRCF](https://www.srcf.net/)) and rename the directory

```bash
cd public_html
git clone https://github.com/atokolyi/raven_event_planner.git
mv raven_event_planner finder
```

2. Create a mysql database (i.e. using [phpmyadmin](https://www.srcf.net/phpmyadmin/)) and tables

```bash
mysql db_name < create_db.sql
```

3. Create a google maps API key and folder

https://developers.google.com/maps/gmp-get-started

```bash
mkdir maps
```

4. Modify the `.htaccess` for your domain

5. Input the private details (below)



### Private details

Create a file called `private.php` and input the keys and details created above using the following template:

```php
<?php

# Name for title and h1 of the web-app
$TITLE = "Queens' MCR Finder";

# Keys, mysql details, create tables in readme
$link = mysqli_connect("localhost","XX_USER","XX_PWD","XX_DB") or die("Could not connect to host");
$MAP_KEY = "XX_KEY";

# Admin CRSIDs for event moderation
$ADMIN = array("aiit2");

# Max amount of people per event
$EVENT_MAX = 6;

?>
```



### Customisation and CSS

1. Create favicons

I like to use [favicon.io](https://favicon.io/), download the created files and place them in the directory.

2. Modify the colour scheme

The default will be Queens' greens, change the `nth-child(even)` and `nth-child(odd)` to a slightly darker and lighter shade respectively of the same colour for a similar effect.