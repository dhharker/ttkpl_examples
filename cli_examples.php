#!/usr/bin/php
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cli_examples
 *
 * @author david
 */

$path = dirname(realpath (__FILE__)).'/examples/';
echo "\n";

// Get user input
$opts = getopt ("e::i::");
$dieHelp = FALSE;

// Get usable examples
$examples = scandir($path);
foreach ($examples as $ei => &$ex)
    if (preg_match ("/^([a-z0-9_]+)\.example\.php$/", $ex, $m) < 1)
        unset ($examples[$ei]);
    else
        $ex = $m[1];
$examples = array_values ($examples);

// Validate user input
if (!isset ($opts['e']) && !isset ($opts['i']))
    $dieHelp = "Please specify an example.";
elseif (isset ($opts['i']) && !isset ($examples[$opts['i']]))
    $dieHelp = "Index not found";
elseif (isset ($opts['e']) && !in_array($opts['e'], $examples))
    $dieHelp = "Example name not found";
if ($dieHelp !== FALSE) {
    echo "!" . $dieHelp . "\nUsage:\n\tcli_examples.php -i<array index of example>\n\tcli_examples.php -e<name of example>\nAvailable examples:\n" . print_r ($examples, TRUE);
    exit (0);
}
elseif (isset ($opts['i']))
    $example = $examples[$opts['i']];
elseif (isset ($opts['e']))
    $example = $opts['e'];

// Load example
$expath = $path.$example.'.example.php';
echo "Loading example: $expath...\n";
include $expath or die ("Failed to load example!");

require_once 'lib/ttkpl/lib/ttkpl.php';


echo "\n";
?>
