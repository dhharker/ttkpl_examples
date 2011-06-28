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
if ($dieHelp !== FALSE)
    die ("!" . $dieHelp . "\nUsage:\n\tcli_examples.php -i<array index of example>\n\tcli_examples.php -e<name of example>\nAvailable examples:\n" . print_r ($examples, TRUE));
elseif (isset ($opts['i']))
    $example = $examples[$opts['i']];
elseif (isset ($opts['e']))
    $example = $opts['e'];

define ('EXAMPLE_NAME', $example);

// Define a few helper functions for the example
function examples_data_path () {
    return dirname(realpath (__FILE__)).'/example_data/';
}
function examples_output_path ($name = NULL) {
    static $n = NULL; $n = ($n === NULL) ? $n : $name; // Only allow name to be set once
    $path = dirname(realpath (__FILE__)).'/output/' . $n . '/';
    if (!is_dir ($path))
        mkdir ($path) or die ("Couldn't create $path to save output :-(\n");
}

// Load TTKPL
echo "Loading TTKPL...\n";
require_once 'lib/ttkpl/lib/ttkpl.php';
// Load example
$expath = $path.$example.'.example.php';
echo "\nLoading example: $expath...\n";
include ($expath);

// Example file can take it from here!

echo "\nFinito!\n";
?>
