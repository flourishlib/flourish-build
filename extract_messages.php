<?php
$source = '../classes/';
$files = array_diff(scandir($source), array('.', '..', 'flourish.rev'));

$max_message_length = 0;
$messages = array();
foreach ($files as $orig_file) {
	$contents = file_get_contents($source . $orig_file);
	preg_match_all('#(?<=Exception\()\s*\'(.*?)\'|(?<=Exception\()\s*"(.*?)"|(?<=::compose\()\s*\'(.*?)\'|(?<=::compose\()\s*"(.*?)"#', $contents, $matches, PREG_SET_ORDER);
	$start_pos = 0;
	foreach ($matches as $match) {
		$match_text = join('', array_slice($match, 1, 4));
		$pos = strpos($contents, $match_text, $start_pos);
		$line_num = substr_count(substr($contents, 0, $pos), "\n") + 1;
		
		if (!isset($messages[$match_text])) {
			$messages[$match_text] = array();
		}
		$messages[$match_text][] = $orig_file . ':' . $line_num;
		
		$match_text = str_replace('\\$', '$', $match_text);
		$match_text = str_replace('\\"', '"', $match_text);
		$match_text = str_replace('\\\'', '\'', $match_text);
		$len = strlen(str_replace("'", "\\'", $match_text));
		if ($len > $max_message_length) {
			$max_message_length = $len;
		}
		
		$start_pos = $pos + 2;
	}
}

function keycmp($a, $b)
{
	return strcasecmp($a, $b);
}

uksort($messages, "keycmp");

$messages_php  = "<?php\n\$translations = array(";
$messages_html = "<table><tr><th>Message</td><th style=\"width: 30%;\">Locations</th></tr>\n";
foreach ($messages as $message => $locations) {
	$hrefs = array();
	foreach ($locations as $location) {
		list ($class, $line) = explode(':', $location);
		$hrefs[] = '<a href="https://github.com/flourishlib/flourish-classes/blob/master/' . $class . '#L' . $line . '">' . $class . ', line ' . $line . '</a>';	
	}
	
	$message = str_replace('\\$', '$', $message);
	$message = str_replace('\\"', '"', $message);
	$message = str_replace('\\\'', '\'', $message);
	
	$messages_php .= "\n\t" . str_pad("'" . str_replace("'", "\\'", $message) . "'", $max_message_length + 2, ' ', STR_PAD_RIGHT) . " => '',";
	
	$messages_html .= "<tr><td> " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . " </td><td> " . join('<br /> ', $hrefs) . " </td></tr>\n"; 		
}
$messages_php   = substr($messages_php, 0, -1) . "\n);";
$messages_html .= "</table>\n";

$hash = `git --work-tree=../classes --git-dir=../classes/.git rev-parse HEAD`;
$hash = substr($hash, 0, 8);
file_put_contents('../flourishlib.com/messages/' . $hash . '.phps', $messages_php);

$tag = `git --work-tree=../classes --git-dir=../classes/.git tag`;
$tag = trim($tag);
if ($tag) {
	file_put_contents('../flourishlib.com/messages/' . $tag . '.phps', $messages_php);
}

file_put_contents('../flourishlib.com/messages/table.html', $messages_html);