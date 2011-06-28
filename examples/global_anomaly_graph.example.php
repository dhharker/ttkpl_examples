<?php

/**
 * Draws a graph of global temperature anomaly over time
 */

// Output path is created automatically if it doesn't exist, full path returned.
$output_path = examples_output_path(EXAMPLE_NAME);

/**
 * First define what time period we are interested in
 * Palaeotime objects contain a time in years "b.p." or "before present" where the "present" is
 * arbitrarily set at 1950AD.
 */
$dateRecent = new palaeoTime("NOW");
$dateAncient = new palaeoTime(10000);


/**
 * Next we need to look up the global anomaly (°C hotter or colder than present) over time. The
 * temperatures class contains both a continuous time series of global anomalies and global annual
 * temperature models at 0bp ("pre-industrial control"), 6kbp ("mid holocene"), 21kbp (LGM) and is
 * capable of interpolating temperatures at essentially arbitrary precision in both time and space.
 * Pretty cool, huh? Luckily it's not too hard to use:
 */
$temps = new temperatures ();




?>