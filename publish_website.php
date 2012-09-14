<?php
chdir(dirname(__FILE__));

`php _publish_site.php`;
`php _publish_api.php`;
`php _generate_single_page.php`;

chdir('../flourishlib.com');

`git pull -q origin master`;
`git commit -q -a -m "Published latest content from flourish-site and flourish-classes"`;
`git push -q origin master`;

chdir('../build');