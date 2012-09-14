# Automation Scripts

These PHP scripts are used to queue tests to be run and publish changes to
http://flourishlib.com.


## Contributor Scripts

These scripts require push access to the repositories at
https://github.com/flourishlib.

Queues the current commit in `../classes/` to be tested using the current commit
in `../tests/`.

```
php queue_tests.php
```

Compiles the website (`../site/`) and API docs (from `../classes/`) to
`../flourishlib.com/`. The server-side scripts will deploy these changes.

```
php publish_website.php
```


## Dependencies

The following commands require you to have the php and git binaries in your
`PATH`. Your PHP install will need the `dom` extension to generate the API
documentation.

Windows users can get command line PHP and git from the following URLs:

 - http://code.google.com/p/msysgit/downloads/list
 - http://windows.php.net/download/

A simple way to edit your path on Windows it Path Editor
http://www.redfernplace.com/software-projects/patheditor/.


## Server-Side Scripts

Runs all of the queued tests from `../tests-results/todo.json`.
This should only be run on servers that have ssh access to the test VMs.

```
php run_tests.php
```

Deploys the master from `../flourishlib.com/` to http://flourishlib.com.

```
php deploy_website.php
```

## Internals

Runs phpdoc and renders `/api/` pages in `../flourishlib.com/` from
`../classes/`.

```
php _publish_api.php
```

Renders pages from `../site/` to `../flourishlib.com/`.

```
php _publish_site.php -f
```

Pulls out all translatable messages from `../classes/` into
`../flourishlib.com/messages/`.

```
php _extract_messages.php
```

Compiles the single-page HTML docs into `../flourishlib.com/offline_docs/`.

```
php _generate_single_page.php
```