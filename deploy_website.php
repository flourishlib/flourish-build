<?php
$pid_file = dirname(__FILE__) . '/deploy_website.pid';

if (file_exists($pid_file)) {
    exit;
}

chdir(dirname(__FILE__));

touch($pid_file);

chdir('../flourishlib.com');

`git pull -q origin master`;

$head_hash = `git rev-parse HEAD`;
$head_hash = trim($head_hash);

$deployed_hash_filename = '/var/www/flourishlib.com/deployed.sha1';
$filesystem_hash = file_get_contents($deployed_hash_filename);

if ($filesystem_hash != $head_hash) {
	`git checkout-index -a -f --prefix=/var/www/flourishlib.com/docroot/`;
	file_put_contents($deployed_hash_filename, $head_hash);
}

chdir('../build');

unlink($pid_file);
