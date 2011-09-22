<?php

/**
 * Example #4
 * How burial affects the temperature of a sample and how to describe it.
 */

/**
 * There are a few classes in TTKPL which help us model the effects of burial. Burial is modelled as
 * a series of layers (i.e. of soil) which each have a thickness in metres and a thermal diffusivity
 * in m^2·day^-1. The order of these layers doesn't influence their net effect on the sample temperature
 * (however in reality the chemical composition, hydrodrynamic environment, pH etc. all play a part
 * and so order could matter, but these are not currently modelled by TTKPL so never mind).
 *
 * You can think of thermal diffusivity as "how fast changes in temperature at the surface will
 * permeate down through the soil layer".
 */
$peatySoilTd = scalarFactory::makeThermalDiffusivity (0.1);
$claySoilTd = scalarFactory::makeThermalDiffusivity (0.18);

/**
 * Now we've defined the thermal diffusivities of a couple of soil typse, we create objects to
 * represent those soil layers:
 */
$thickPeat = new thermalLayer(
        scalarFactory::makeMetres(4),
        $peatySoilTd, "Hypothetical dry, peaty soil.");
$thinClay = new thermalLayer(
        scalarFactory::makeMetres(1),
        $claySoilTd, "Hypothetical dry, clay soil.");

/**
 * A sample is buried in a "burial environment". This adds up all the layers and calculates their
 * combined effect given a surface temperature sine object.
 */
$littleBurial = new burial();
$littleBurial->addThermalLayer($thinClay);

$moderateBurial = new burial();
$moderateBurial->addThermalLayer($thickPeat);

$muchBurial = new burial();
$muchBurial->addThermalLayer($thinClay);
$muchBurial->addThermalLayer($thickPeat);

/**
 * For this example, we'll look at the effects of burial in a constant climate. We can create a sine
 * object to represent this (this feature is useful for modelling storage e.g. in a museum). Here we
 * describe a climate where the average daily temperature over the year is 10°C and where the
 * variation in the daily temperature over the year is 12°C (i.e. from 4 to 16°C). The last parameter
 * of the setGenericSine method is the offset in days at which the minimum occurs. This isn't currently
 * used for anything, but just in case you were curious...
 *
 * Notice how we're using the scalarFactory to provide us with pre-configured scalars in which to
 * store the values of and explicitly specify the units of our parameters.
 */
$climate = new sine();
$climate->setGenericSine(
        scalarFactory::makeCentigradeAbs(10),
        scalarFactory::makeKelvinAnomaly(12),
        scalarFactory::makeDays(250)
        );

$tempDir = examples_output_path(EXAMPLE_NAME);

$plot = new ttkplPlot("The effects of burial on temperature amplitude");

$plot->labelAxes("days", "°C")->setGrid('y')
        ->setData("Unburied surface temperature", 0)
        ->setData("Average daily temperature buried under " . $littleBurial, 1)
        ->setData("Average daily temperature buried under " . $moderateBurial, 2)
        ->setData("Average daily temperature buried under " . $muchBurial, 3);

for ($day = 1; $day <= sine::yearLength; $day++)
    $plot->addData($day, $climate->getValue($day) + scalarFactory::kelvinOffset, 0)
            ->addData($day, $littleBurial->getBufferedSine($climate)->getValue($day) + scalarFactory::kelvinOffset, 1)
            ->addData($day, $moderateBurial->getBufferedSine($climate)->getValue($day) + scalarFactory::kelvinOffset, 2)
            ->addData($day, $muchBurial->getBufferedSine($climate)->getValue($day) + scalarFactory::kelvinOffset, 3);


$filename = examples_output_path(EXAMPLE_NAME) . 'effects_of_burial_on_amplitude.png';
$plot->plot($filename);



?>
