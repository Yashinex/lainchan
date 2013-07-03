#!/usr/bin/php
<?php

/*
 *  i18n_extract.php - extracts the strings and updates all locales
 *
 *  Options:
 *    -l [locale], --locale=[locale]
 *      Updates only [locale] locale. If it does not exist yet, we create a new directory.
 *      
 */

require dirname(__FILE__) . '/inc/cli.php';

// parse command line
$opts = getopt('l:', Array('locale:'));
$options = Array();

$options['locale'] = isset($opts['l']) ? $opts['l'] : (isset($opts['locale']) ? $opts['locale'] : false);

$locales = glob("inc/locale/*");
$locales = array_map("basename", $locales);

if ($options['locale']) $locales = array($options['locale']);


foreach ($locales as $loc) {
	if (file_exists ($locdir = "inc/locale/".$loc)) {
		if (!is_dir ($locdir)) {
			continue;
		}
	}
	else {
		mkdir($locdir);
		mkdir($locdir."/LC_MESSAGES");
	}

	// Generate tinyboard.po
	if (file_exists($locdir."/LC_MESSAGES/tinyboard.po"))	$join = "-j";
	else							$join = "";
	`cd $locdir/LC_MESSAGES;
         xgettext -d tinyboard -L php --from-code utf-8 $join -c $(find ../../../../ -name \*.php)`;

	// Generate javascript.po
	`cd tools/inc/lib/jsgettext/;
         php jsgettext.php -o ../../../../$locdir/LC_MESSAGES/javascript.po $(find ../../../../js/ -name \*.js)`;
}
