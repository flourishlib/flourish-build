These PHP scripts are used to publish flourishlib.com and the content on the site. They should be run right before tagging a release.

The following commands require you to have the php and git binaries in your PATH. Your PHP install will need the dom extension to generate the API documentation.

Windows users can get command line PHP and git from the following URLs:
 - http://code.google.com/p/msysgit/downloads/list
 - http://windows.php.net/download/
A simple way to edit your path on Windows it Path Editor:
 - http://www.redfernplace.com/software-projects/patheditor/

# Queues the current commit in ../classes/ to be tested using the current commit in ../tests/
# After this, the ../tests-results/ repo needs to be comitted and pushed
php queue_tests.php

# Runs all of the queued tests from ../tests-results/todo.json
# This should only be run on servers that have ssh access to the test VMs
# Does not commit or push the git repo
php run_tests.php

# Runs phpdoc and renders /api/ pages in ../flourishlib.com/
php publish_api.php

# Renders pages from ../site/ to ../flourishlib.com/
php publish_site.php -f

# Pulls out all translatable messages from ../classes/ into ../flourishlib.com/messages/
php extract_messages.php

# Compiles the single-page HTML docs into ../flourishlib.com/offline_docs/
php generate_single_page.php