These PHP scripts are used to publish flourishlib.com and the content on the site. They should be run right before tagging a release.

# Runs phpdoc and renders /api/ pages
php publish_api.php

# Renders other pages on the site
php publish_site.php -f

# Pulls out all translatable messages into /messages/
php extract_messages.php

# Compiles the single-page HTML docs
php generate_single_page.php