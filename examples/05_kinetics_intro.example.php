<?php

/**
 * Example #5
 * Introduces the kinetics classes. For a quick overview of what we're doing here, have a look at
 * http://en.wikipedia.org/wiki/Arrhenius_equation
 */


/**
 * For any reaction we want to model we have to know the pre-exponential factor and energy of
 * activation for the reaction. We can then work out the temperature dependent rate of that reaction
 * in mol/sec. The kinetics class can go the arrhenius equation in both directions and stores these
 * parameters along with a description of the reaction. Let's define DNA scission by depurination:
 */
$depurination = new kinetics (
        scalarFactory::makeKilojoulesPerMole(126940),
        scalarFactory::makeSeconds(17745329175.856213),
        "DNA depurination (bone)"
        );

/**
 * Let's plot rate of reaction against temperature to fully understand the importance of not keeping
 * samples in hot places (the relationship is exponential)!
 */
$plot = new ttkplPlot("Rate of Reaction vs. Temperature");
$plot->labelAxes("Temperature (°C)", "k (mol·s^-1)")->setGrid('xy')
        ->setData("DNA Depurination");

for ($temperature = -10; $temperature <= 35; $temperature++)
    $plot->addData(
            $temperature,
            $depurination->getRate(
                    scalarFactory::makeCentigradeAbs($temperature))
            );

$plot->plot("rate_of_reaction_temperature.png");

?>