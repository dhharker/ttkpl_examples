<?php


/**
 * Example #6
 * In the last example we saw how the rate of reaction is related to temperature. Because this
 * relationship is not linear, we cannot take the average rate of reaction over a period of varying
 * temperature by calculating the rate of reaction at the mean temperature. Instead we sample the
 * rate of reaction and take the mean of these rates, optionally converting this back into a
 * temperature. This temperature is known as the "effective temperature". We'll use DNA depurination
 * as our reaction (covered in the previous example).
 */
$depurination = new kinetics (
        scalarFactory::makeKilojoulesPerMole(126940),
        scalarFactory::makeSeconds(17745329175.856213),
        "DNA depurination (bone)"
        );


/**
 * Let's compare a constant temperature with a sine modelled temperature over a year, and verify the
 * former while investigating the latter by converting back from a mean rate to effective temperature.
 *
 * First, a variable climate of 10±6°C over the year
 */
$varClimate = new sine();
$varClimate->setGenericSine(
        scalarFactory::makeCentigradeAbs(10),
        scalarFactory::makeKelvinAnomaly(12),
        scalarFactory::makeDays(0)
        );


/**
 * And lets say it's a constant 10°C in our temperature controlled store room or whatever...
 */
$storage = scalarFactory::makeCentigradeAbs(10);



/**
 * Now to find out how much the DNA decays in a year, and make some more graphs!
 * Setup us the plot first
 */
$tempDir = examples_output_path(EXAMPLE_NAME);
$plot = new ttkplPlot("Effective Temperature");
$plot->labelAxes("Day", "Temperature (°C)", '', "k (mol·s^-1)")->setGrid('x')
        ->setData("Mean daily air temperature (°C)", 0, 'x1y1')
        ->setData("Rate of depurination at given daily temperature (mol·s^-1)", 1, 'x1y2')
        ->setData("Rate of depurination at 10°C (mol·s^-1)", 2, 'x1y2')
        ->setData("Effective Temperature (°C)", 3, 'x1y1');

/**
 * First lets draw a couple of horizontal lines across the graph to highlight 10C and the rate at 10C
 * otherwise it's not so clear...
 */
$sR = $depurination->getRate($storage)->getValue();
$sT = $storage->getValue();
foreach (array (1,365) as $day)
    $plot->addData($day, $sR, 2);

$varRate = array ();
for ($day = 1; $day <= 365; $day++) {
    /**
     * How fast is the DNA decaying today?
     * Note our use of the sine::getValueScalar method instead of sine::getValue, which returns a
     * double, which the kinetics class doesn't want because it's a bit ambiguous.
     */
    $tToday = $varClimate->getValueScalar ($day);
    $rToday = $depurination->getRate($tToday);
    $varRate[] = $rToday->getValue();

    $plot->addData($day, $tToday->getValue() + scalarFactory::kelvinOffset, 0)
         ->addData($day, $rToday->getValue(), 1);
}

/**
 * Next we'll calculate the effective temperature. The temperature which causes the k to equal the
 * mean of k over the year of fluctuating temperature.
 */
$meanDailyK = scalarFactory::makeMolesPerSecond(cal::mean($varRate));
$effectiveTemperature = scalarFactory::makeCentigradeAbs($depurination->getTempAtRate($meanDailyK));
// And plot it
foreach (array (1,365) as $day)
    $plot->addData($day, $effectiveTemperature->getValue(), 3);

// Save our plot...
$fn = $tempDir . "rate_of_reaction_effective_temperature.png";
$plot->plot($fn);


/**
 * And let's output some stuff to the console just for giggles...
 */
printf ("Over 365 days sine %s, effective temperature is %0.1f°C." .
        " (i.e. the sample is as degraded after a year in the sine temperatures as it would be after a year constantly at the effective temperature)" .
        "\nEffective temperature of a sample" .
        " at a constant 10°C is %0.1f (it really should be 10!)\n",
        $varClimate,
        $effectiveTemperature->getValue(),
        scalarFactory::makeCentigradeAbs($depurination->getTempAtRate($depurination->getRate($storage)))->getValue()
        );


?>