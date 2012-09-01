These PHP scripts are used to publish flourishlib.com and the content on the site. They should be run right before tagging a release.

The following commands require you to have the php and git binaries in your PATH. Your PHP install will need the dom extension to generate the API documentation.

Windows users can get command line PHP and git from the following URLs:
 - http://code.google.com/p/msysgit/downloads/list
 - http://windows.php.net/download/
A simple way to edit your path on Windows it Path Editor:
 - http://www.redfernplace.com/software-projects/patheditor/

# Runs phpdoc and renders /api/ pages
php publish_api.php

# Renders other pages on the site
php publish_site.php -f

# Pulls out all translatable messages into /messages/
php extract_messages.php

# Compiles the single-page HTML docs
php generate_single_page.php