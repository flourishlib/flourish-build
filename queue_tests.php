<?php
$classes_rev = 'HEAD';
$tests_rev = 'HEAD';

// Allow passing hash references for the classes and tests
if (!empty($_SERVER['argc'])) {
    foreach (array_slice($_SERVER['argv'], 1) as $i => $arg) {
        if ($i == 0) {
            $classes_rev = $arg;
        } elseif ($i == 1) {
            $tests_rev = $arg;
        }
    }
}

$classes_hash = `git --work-tree=../classes --git-dir=../classes/.git rev-parse $classes_rev`;
$classes_hash = trim($classes_hash);

$tests_hash = `git --work-tree=../tests --git-dir=../tests/.git rev-parse $tests_rev`;
$tests_hash = trim($tests_hash);

$json = file_get_contents('../tests-results/todo.json');
$todo = json_decode($json, TRUE);
$todo[$classes_hash] = $tests_hash;
$json = json_encode($todo);
$json = str_replace('{', "{\n\t", $json);
$json = str_replace(',', ",\n\t", $json);
$json = str_replace('}', "\n}", $json);
file_put_contents('../tests-results/todo.json', $json);

$_ = `git --work-tree=../tests-results --git-dir=../tests-results/.git pull --rebase origin master`;
$_ = `git --work-tree=../tests-results --git-dir=../tests-results/.git commit -a -m "Queued run for $classes_hash"`;
$_ = `git --work-tree=../tests-results --git-dir=../tests-results/.git push origin master`;