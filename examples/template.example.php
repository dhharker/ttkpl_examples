<?php

/**
 * This is the template for new examples.
 * Examples start with TTKPL already loaded
 * Change this comment to describe what the example does and which parts of TTKPL is explains
 */

// Output path is created automatically if it doesn't exist, full path returned.
$output_path = examples_output_path(EXAMPLE_NAME);
// Returns path to example data. This is shared and returns the full path whether or not it exists.
$data_path = examples_data_path();

echo "Hello, I am a sample example! I will read data from:\n$data_path\n...and write it to:\n$output_path\nBye for now!\n";

?>