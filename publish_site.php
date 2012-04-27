<?php
$destination = '../flourishlib.com';
$source = '../flourish-site';
$dirname = dirname(__file__);

$force = isset($argv[1]) && $argv[1] == '-f' ? TRUE : FALSE;

function render($url, $source, $destination, $original_source=NULL)
{
	global $force;

	// Check the file time of the original to allow pre-processing
	if (!$original_source) {
		$original_source = $source;
	}

	if (!$force && file_exists($destination) && filemtime($original_source) < filemtime($destination)) {
		echo "Skipping $url - latest source already rendered\n";
		return;
	}

	echo "Rendering $url\n";
	`php ../flourish-wiki/render.php $source > $destination`;
}

$start = microtime(TRUE);

render("/", "$source/$source/index.wiki", "$destination/index.html");
render("/About", "$source/$source/About.wiki", "$destination/About.html");
render("/Download", "$source/$source/Download.wiki", "$destination/Download.html");
render("/AdvancedDownload", "$source/$source/AdvancedDownload.wiki", "$destination/AdvancedDownload.html");
render("/Support", "$source/$source/Support.wiki", "$destination/Support.html");
render("/Tests", "$source/$source/Tests.wiki", "$destination/Tests.html");

$blogs = array_diff(scandir('../flourish-site/blog'), array('.', '..'));
foreach ($blogs as $blog) {
	if (!preg_match('#\.wiki$#', $blog)) {
		continue;
	}
	$blog_path = '../flourish-site/blog/' . $blog;
	$html_path = $destination . '/blog/' . str_replace('.wiki', '.html', $blog);
	$blogs_wiki = file_get_contents($blog_path);
	$title = str_replace('.wiki', '', $blog);
	render("/blog/$title", "$blog_path", "$html_path");
}

$docs = array_diff(scandir('../flourish-docs'), array('.', '..'));
foreach ($docs as $doc) {
	$doc_path = '../flourish-docs/' . $doc;
	$html_path = $destination . '/docs/' . str_replace('.wiki', '.html', $doc);
	if (!preg_match('#\.wiki$#', $doc)) {
		continue;
	}
	$docs_wiki = file_get_contents($doc_path);
	$tmp_path = '/var/tmp/' . $doc;

	$page = str_replace('.wiki', '', $doc);
	if (preg_match('#^f[A-Z]#', $doc)) {
		$title = $page . ' – Class Documentation – ';
	} elseif ($doc == 'index.wiki') {
		$title = 'Documentation – ';
	} else {
		$title = $page . ' – General Documentation – ';
	}
	
	$docs_wiki = '<<include path="' . $dirname . '/../flourish-site/partials/header.html" title="' . $title . 'Flourish">>' . "\n\n" . $docs_wiki .
		"\n\n" . '<<include path="' . $dirname . '/../flourish-site/partials/footer.html">>';
	file_put_contents($tmp_path, $docs_wiki);
	render("/docs/$page", "$tmp_path", "$html_path", "$doc_path");
	unlink($tmp_path);
}

echo "Copying /favicon.ico\n";
`cp $source/favicon.ico $destination/`;

echo "Copying /img/\n";
`cp -R $source/img $destination/`;

echo "Copying /css/\n";
`cp -R $source/css $destination/`;

echo "Copying /files/\n";
`cp -R $source/files $destination/`;

echo "Copying /js/\n";
`cp -R $source/js $destination/`;

$total = microtime(TRUE) - $start;

echo "Run time: " . round($total, 2) . " seconds\n";