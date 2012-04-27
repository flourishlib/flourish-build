<?php
$destination = '../flourishlib.com/api/';

$tmp_dir = '/var/tmp/flourish-phpdoc/';
if (file_exists($tmp_dir)) {
	passthru("rm -Rf $tmp_dir");
}
mkdir($tmp_dir);

passthru("php ../flourish-phpdoc/generate.php $tmp_dir");

$files = array_diff(scandir($tmp_dir), array('.', '..'));
foreach ($files as $file) {
	if (!preg_match('#\.html$#', $file)) {
		continue;
	}
	$class = str_replace('.html', '', $file);
	$content = file_get_contents($tmp_dir . $file);

	$title = $class . ' – API Reference – Flourish';

	$header = file_get_contents('../flourish-site/partials/header.html');
	$header = str_replace('{{ title }}', $title, $header);
	$header .= '<script src="/js/api.js"></script>';

	$footer = file_get_contents('../flourish-site/partials/footer.html');

	file_put_contents('../flourishlib.com/api/' . $file, $header . $content . $footer);
}

passthru("rm -Rf $tmp_dir");