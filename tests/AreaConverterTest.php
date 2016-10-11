<?php

use IFP\Adverts\AreaConverter;

class AreaConverterTest extends PHPUnit_Framework_TestCase
{
    private function assertNumber($expected, $actual)
    {
        if(!is_numeric($actual)) {
            $this->fail("$actual is not numeric");
        }

        //echo PHP_EOL . $expected . ' : ' . $actual . PHP_EOL;
        $number_of_expected_decimal_places = strlen(substr(strrchr($expected, "."), 1));
        $number_of_actual_decimal_places = strlen(substr(strrchr($actual, "."), 1));

        $this->assertEquals(0, bccomp($expected, $actual, $number_of_expected_decimal_places));
        $this->assertEquals($number_of_expected_decimal_places, $number_of_actual_decimal_places);
    }

    // m² -> m²
    public function testTheAreaCanBeConvertedFromSquareMetresToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(100, $area_converter->from("100", 'm²')->to('m²')->value());
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToSquareMetresAndRoundedDownToNearestSquareMetre()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(100, $area_converter->from(100.9, 'm²')->to('m²')->value());
    }

    // ac -> m²
    public function testTheAreaCanBeConvertedFromAcresToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(404686, $area_converter->from(100, 'ac')->to('m²')->value());
    }

    public function testTheAreaCanBeConvertedFromAcresToSquareMetresAndRoundedDownToNearestSquareMetre()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(4046, $area_converter->from(1, 'ac')->to('m²')->value());
    }

    // ha -> m²
    public function testTheAreaCanBeConvertedFromHectaresToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(10000, $area_converter->from(1, 'ha')->to('m²')->value());
    }

    public function testTheAreaCanBeConvertedFromHectaresToSquareMetresAndRoundedDownToNearestMetre()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(9912, $area_converter->from(0.991234, 'ha')->to('m²')->value());
    }

    // invalid -> m²
    public function testAMessageOfUnknownIsReturnedIfTheValueConvertedToSquareMetresIsNotNumeric()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('unknown', $area_converter->from(null, 'ha')->to('m²')->value());
        $this->assertEquals('unknown', $area_converter->from('foo', 'ha')->to('m²')->value());
        $this->assertEquals('unknown', $area_converter->from(['foo'], 'ha')->to('m²')->value());
        $this->assertEquals('unknown', $area_converter->from(new stdClass(), 'ha')->to('m²')->value());
    }

    // ac -> ac
    public function testTheAreaCanBeConvertedFromAcresToAcres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(15, $area_converter->from(15, 'ac')->to('ac')->value());
    }

    public function testTheAreaCanBeConvertedFromAcresToAcresAndRoundedDownToNearestOneOrTwoDecimalPlacesDependingOnValue()
    {
        $area_converter = new AreaConverter();

        //Changed to smarter decimal places depending on value, to avoid wrapping onto new lines on mobile view
//        $this->assertNumber(15.76, $area_converter->from(15.769, 'ac')->to('ac')->value());
        $this->assertNumber(1.57, $area_converter->from(1.5769, 'ac')->to('ac')->value());
        $this->assertNumber(15.7, $area_converter->from(15.769, 'ac')->to('ac')->value());
        $this->assertNumber(157,  $area_converter->from(157.69, 'ac')->to('ac')->value());
        $this->assertNumber(1578, $area_converter->from(1578.769, 'ac')->to('ac')->value());

    }

    // m² -> ac
    public function testTheAreaCanBeConvertedFromSquareMetresToAcres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1, $area_converter->from(4046.86, 'm²')->to('ac')->value());
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToAcresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1.22, $area_converter->from(4970, 'm²')->to('ac')->value());
    }

    // ha -> ac
    public function testTheAreaCanBeConvertedFromHectaresToAcresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(2.47, $area_converter->from(1, 'ha')->to('ac')->value());
    }

    // invalid -> ac
    public function testAMessageOfUnknownIsReturnedIfTheValueConvertedToAcresIsNotNumeric()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('unknown', $area_converter->from(null, 'ha')->to('ac')->value());
        $this->assertEquals('unknown', $area_converter->from('foo', 'ha')->to('ac')->value());
        $this->assertEquals('unknown', $area_converter->from(['foo'], 'ha')->to('ac')->value());
        $this->assertEquals('unknown', $area_converter->from(new stdClass(), 'ha')->to('ac')->value());
    }

    // ha -> ha
    public function testTheAreaCanBeConvertedFromHectaresToHectares()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(15, $area_converter->from(15, 'ha')->to('ha')->value());
    }

    public function testTheAreaCanBeConvertedFromHectaresToHectaresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        //Changed to smarter decimal places depending on value, to avoid wrapping onto new lines on mobile view
//        $this->assertNumber(15.76, $area_converter->from(15.769, 'ha')->to('ha')->value());
        $this->assertNumber(1.57, $area_converter->from(1.5769, 'ha')->to('ha')->value());
        $this->assertNumber(15.7, $area_converter->from(15.769, 'ha')->to('ha')->value());
        $this->assertNumber(157,  $area_converter->from(157.69, 'ha')->to('ha')->value());
        $this->assertNumber(1576, $area_converter->from(1576.9, 'ha')->to('ha')->value());
    }

    // m² -> ha
    public function testTheAreaCanBeConvertedFromSquareMetresToHectares()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1, $area_converter->from(10000, 'm²')->to('ha')->value());
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToHectaresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1.22, $area_converter->from(12299, 'm²')->to('ha')->value());
    }

    // ac -> ha
    public function testTheAreaCanBeConvertedFromAcresToHectaresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1.34, $area_converter->from(3.311212, 'ac')->to('ha')->value());
    }

    // invalid -> ha
    public function testAMessageOfUnknownIsReturnedIfTheValueConvertedToHectaresIsNotNumeric()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('unknown', $area_converter->from(null, 'ha')->to('ha')->value());
        $this->assertEquals('unknown', $area_converter->from('foo', 'ha')->to('ha')->value());
        $this->assertEquals('unknown', $area_converter->from(['foo'], 'ha')->to('ha')->value());
        $this->assertEquals('unknown', $area_converter->from(new stdClass(), 'ha')->to('ha')->value());
    }

    // other stuff
    public function testTheConversionUnitDefaultsToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('m²', $area_converter->conversionUnitName());
        $this->assertEquals('m²', $area_converter->conversionUnitSymbol());
        $this->assertEquals(true, $area_converter->isUnit('m²'));
    }

    public function testTheConversionUnitCanBeSetToAcres()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('ac');

        $this->assertEquals('acres', $area_converter->conversionUnitName());
        $this->assertEquals('ac', $area_converter->conversionUnitSymbol());
        $this->assertEquals(true, $area_converter->isUnit('ac'));
    }

    public function testTheConversionUnitCanBeSetToAcreInSingular()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('ac');
        $area_converter->from(1, 'ac');

        $this->assertEquals('acre', $area_converter->conversionUnitName());
        $this->assertEquals('ac', $area_converter->conversionUnitSymbol());
        $this->assertEquals(true, $area_converter->isUnit('ac'));
    }

    public function testTheConversionUnitCanBeSetToHectares()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('ha');

        $this->assertEquals('ha', $area_converter->conversionUnitName());
        $this->assertEquals('ha', $area_converter->conversionUnitSymbol());
        $this->assertEquals(true, $area_converter->isUnit('ha'));
    }

    public function testTheConversionUnitCanBeSetToHectareInSingular()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('ha');
        $area_converter->from(1, 'ha');

        $this->assertEquals('ha', $area_converter->conversionUnitName());
        $this->assertEquals('ha', $area_converter->conversionUnitSymbol());
        $this->assertEquals(true, $area_converter->isUnit('ha'));
    }

    public function testTheConversionUnitCanBeSetToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('ha');
        $area_converter->selectConversionUnit('m²');

        $this->assertEquals('m²', $area_converter->conversionUnitName());
        $this->assertEquals(true, $area_converter->isUnit('m²'));
    }

    public function testTheConversionUnitCannotBeSetToAnInvalidUnit()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('foo');

        $this->assertEquals('m²', $area_converter->conversionUnitName());
    }

    public function testTheAreaCanBeConvertedFromAcresToTheConversionUnitOfHectares()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('ha');

        $this->assertNumber(1.34, $area_converter->from(3.311212, 'ac')->toSelectedConversionUnit()->value());
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToTheConversionUnitOfAcres()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('ac');

        $this->assertNumber(1.22, $area_converter->fromSquareMetres(4970)->toSelectedConversionUnit()->value());
    }

    public function testFormattedValuePlural()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('10000 m²',
            $area_converter->from(1, 'ha')->to('m²')->formattedValueAndUnit());

        $this->assertEquals('10 ha',
            $area_converter->from(100000, 'm²')->to('ha')->formattedValueAndUnit());

        $this->assertEquals('10 acres',
            $area_converter->from(40468.6, 'm²')->to('ac')->formattedValueAndUnit());
    }

    public function testPreciseFormattedValue()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('56780 m²', $area_converter->from(5.678, 'ha')->to('m²')->formattedValueAndUnit());

        $this->assertEquals('5.67 ha', $area_converter->from(56789, 'm²')->to('ha')->formattedValueAndUnit());
        $this->assertEquals('56.7 ha', $area_converter->from(567890, 'm²')->to('ha')->formattedValueAndUnit());
        $this->assertEquals('567 ha', $area_converter->from(5678900, 'm²')->to('ha')->formattedValueAndUnit());

        $this->assertEquals('1.23 acres', $area_converter->from(4996, 'm²')->to('ac')->formattedValueAndUnit()); //4996 m² = 1.2345 acre
        $this->assertEquals('12.3 acres', $area_converter->from(49960, 'm²')->to('ac')->formattedValueAndUnit()); //= 12.345 acre
        $this->assertEquals('123 acres', $area_converter->from(499600, 'm²')->to('ac')->formattedValueAndUnit()); //= 123.45 acre
    }

    public function testFormattedValueIsUnknownWhenValueNotSetYet()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals("unknown", $area_converter->formattedValueAndUnit());
    }

    public function testFormattedValueSingular()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('1 m²',
            $area_converter->from(1, 'm²')->to('m²')->formattedValueAndUnit());

        $this->assertEquals('1 ha',
            $area_converter->from(10000, 'm²')->to('ha')->formattedValueAndUnit());

        $this->assertEquals('1 acre',
            $area_converter->from(0.4047, 'ha')->to('ac')->formattedValueAndUnit());
    }

    public function testFormattedValueZero()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('0 m²',
            $area_converter->from(0, 'm²')->formattedValueAndUnit());

        $this->assertEquals('0 m²',
            $area_converter->from(0, 'm²')->to('m²')->formattedValueAndUnit());

        $this->assertEquals('0 ha',
            $area_converter->from(0, 'm²')->to('ha')->formattedValueAndUnit());

        $this->assertEquals('0 acres',
            $area_converter->from(0, 'ha')->to('ac')->formattedValueAndUnit());
    }

    public function testFormattedValueWhenNotConvertedYet()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('100 m²',
            $area_converter->from(100, 'm²')->formattedValueAndUnit());

        $this->assertEquals('20 ha',
            $area_converter->from(20, 'ha')->formattedValueAndUnit());

        $this->assertEquals('100 acres',
            $area_converter->from(100, 'ac')->formattedValueAndUnit());
    }

    public function testFormattedBlankValue()
    {
        //Consider throwing an exception here...
        $area_converter = new AreaConverter();

        $this->assertEquals("unknown", $area_converter->formattedValueAndUnit());
    }

    public function testFormattedValueAndUnitOfAreaUnder1HectareShowsBothUnitsWithOptionSet()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('0.5 ha (5000 m²)',
            $area_converter->from(5000, 'm²')->to('ha')->formattedValueAndUnit(['show_additional_conversion_under_1' => true]));

        $this->assertEquals('0.5 acres (2024 m²)',
            $area_converter->from(2024, 'm²')->to('ac')->formattedValueAndUnit(['show_additional_conversion_under_1' => true]));
    }

    public function testFormattedValueHasThousandSeparatorsWhenLargeEnough()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('1,000 m²',
            $area_converter->from(1000, 'm²')->formattedValueAndUnit());

        $this->assertEquals('1,000 ha',
            $area_converter->from(1000, 'ha')->formattedValueAndUnit());

        $this->assertEquals('1,000 acres',
            $area_converter->from(1000, 'ac')->formattedValueAndUnit());

        $this->assertEquals('10,000,000 m²',
            $area_converter->from(1000, 'ha')->to('m²')->formattedValueAndUnit());
    }

    // A slightly contrived test, but this should be tested somehow just in case
    public function testInstanceReturnedByselectConversionUnitMethod()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('ha',
            $area_converter->selectConversionUnit('ha')->conversionUnitName());
    }

    //public function test
}
