<?php

use IFP\Adverts\AreaConverter;

class AreaConverterTest extends PHPUnit_Framework_TestCase
{
    private function assertNumber($expected, $actual)
    {
        //echo PHP_EOL . $expected . ' : ' . $actual . PHP_EOL;
        $number_of_expected_decimal_places = strlen(substr(strrchr($expected, "."), 1));
        $number_of_actual_decimal_places = strlen(substr(strrchr($actual, "."), 1));

        $this->assertEquals(0, bccomp($expected, $actual, $number_of_expected_decimal_places));
        $this->assertEquals($number_of_expected_decimal_places, $number_of_actual_decimal_places);
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(100, $area_converter->from("100", 'm²')->to('SquareMetres'));
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToSquareMetresAndRoundedDownToNearestSquareMetre()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(100, $area_converter->from(100.9, 'm²')->to('SquareMetres'));
    }

    public function testTheAreaCanBeConvertedFromAcresToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(404686, $area_converter->from(100, 'Acres')->to('SquareMetres'));
    }

    public function testTheAreaCanBeConvertedFromAcresToSquareMetresAndRoundedDownToNearestSquareMetre()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(4046, $area_converter->from(1, 'Acres')->to('SquareMetres'));
    }

    public function testTheAreaCanBeConvertedFromHectaresToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(10000, $area_converter->from(1, 'Hectares')->to('SquareMetres'));
    }

    public function testTheAreaCanBeConvertedFromHectaresToSquareMetresAndRoundedDownToNearestMetre()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(9912, $area_converter->from(0.991234, 'Hectares')->to('SquareMetres'));
    }

    public function testAMessageOfUnknownIsReturnedIfTheValueConvertedToSquareMetresIsNotNumeric()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('unknown', $area_converter->from(null, 'Hectares')->to('SquareMetres'));
        $this->assertEquals('unknown', $area_converter->from('foo', 'Hectares')->to('SquareMetres'));
        $this->assertEquals('unknown', $area_converter->from(['foo'], 'Hectares')->to('SquareMetres'));
        $this->assertEquals('unknown', $area_converter->from(new stdClass(), 'Hectares')->to('SquareMetres'));
    }

    public function testTheAreaCanBeConvertedFromAcresToAcres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(15, $area_converter->from(15, 'Acres')->to('Acres'));
    }

    public function testTheAreaCanBeConvertedFromAcresToAcresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(15.76, $area_converter->from(15.769, 'Acres')->to('Acres'));
    }

    public function testTheAreaCanBeConvertedFromAcresToAcresAndRoundedDownToNearestTwoDecimalPlaces2()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1578.76, $area_converter->from(1578.769, 'Acres')->to('Acres'));
    }

    public function testTheAreaCanBeConvertedFromAcresToAcresAndRoundedDownToNearestTwoDecimalPlaces3()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1578.76, $area_converter->from(1578.761, 'Acres')->to('Acres'));
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToAcres()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1, $area_converter->from(4046.86, 'm²')->to('Acres'));
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToAcresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1.22, $area_converter->from(4970, 'm²')->to('Acres'));
    }

    public function testTheAreaCanBeConvertedFromHectaresToAcresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(2.47, $area_converter->from(1, 'Hectares')->to('Acres'));
    }

    public function testAMessageOfUnknownIsReturnedIfTheValueConvertedToAcresIsNotNumeric()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('unknown', $area_converter->from(null, 'Hectares')->to('Acres'));
        $this->assertEquals('unknown', $area_converter->from('foo', 'Hectares')->to('Acres'));
        $this->assertEquals('unknown', $area_converter->from(['foo'], 'Hectares')->to('Acres'));
        $this->assertEquals('unknown', $area_converter->from(new stdClass(), 'Hectares')->to('Acres'));
    }


    public function testTheAreaCanBeConvertedFromHectaresToHectares()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(15, $area_converter->from(15, 'Hectares')->to('Hectares'));
    }

    public function testTheAreaCanBeConvertedFromHectaresToHectaresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(15.76, $area_converter->from(15.769, 'Hectares')->to('Hectares'));
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToHectares()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1, $area_converter->from(10000, 'm²')->to('Hectares'));
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToHectaresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1.22, $area_converter->from(12299, 'm²')->to('Hectares'));
    }

    public function testTheAreaCanBeConvertedFromAcresToHectaresAndRoundedDownToNearestTwoDecimalPlaces()
    {
        $area_converter = new AreaConverter();

        $this->assertNumber(1.34, $area_converter->from(3.311212, 'Acres')->to('Hectares'));
    }

    public function testAMessageOfUnknownIsReturnedIfTheValueConvertedToHectaresIsNotNumeric()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('unknown', $area_converter->from(null, 'Hectares')->to('Hectares'));
        $this->assertEquals('unknown', $area_converter->from('foo', 'Hectares')->to('Hectares'));
        $this->assertEquals('unknown', $area_converter->from(['foo'], 'Hectares')->to('Hectares'));
        $this->assertEquals('unknown', $area_converter->from(new stdClass(), 'Hectares')->to('Hectares'));
    }

    public function testTheConversionUnitDefaultsToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $this->assertEquals('m²', $area_converter->conversionUnit());
        $this->assertEquals('m²', $area_converter->conversionUnitAbbreviation());
        $this->assertEquals(true, $area_converter->isUnit('m²'));
    }

    public function testTheConversionUnitCanBeSetToAcres()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('Acres');

        $this->assertEquals('Acres', $area_converter->conversionUnit());
        $this->assertEquals('ac', $area_converter->conversionUnitAbbreviation());
        $this->assertEquals(true, $area_converter->isUnit('Acres'));
    }

    public function testTheConversionUnitCanBeSetToAcreInSingular()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('Acres');
        $area_converter->from(1, 'Acres');

        $this->assertEquals('Acre', $area_converter->conversionUnit());
        $this->assertEquals('ac', $area_converter->conversionUnitAbbreviation());
        $this->assertEquals(true, $area_converter->isUnit('Acres'));
    }

    public function testTheConversionUnitCanBeSetToHectares()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('Hectares');

        $this->assertEquals('Hectares', $area_converter->conversionUnit());
        $this->assertEquals('ha', $area_converter->conversionUnitAbbreviation());
        $this->assertEquals(true, $area_converter->isUnit('Hectares'));
    }

    public function testTheConversionUnitCanBeSetToHectareInSingular()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('Hectares');
        $area_converter->from(1, 'Hectares');

        $this->assertEquals('Hectare', $area_converter->conversionUnit());
        $this->assertEquals('ha', $area_converter->conversionUnitAbbreviation());
        $this->assertEquals(true, $area_converter->isUnit('Hectares'));
    }

    public function testTheConversionUnitCanBeSetToSquareMetres()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('Hectares');
        $area_converter->selectConversionUnit('m²');

        $this->assertEquals('m²', $area_converter->conversionUnit());
        $this->assertEquals(true, $area_converter->isUnit('m²'));
    }

    public function testTheConversionUnitCannotBeSetToAnInvalidUnit()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('foo');

        $this->assertEquals('m²', $area_converter->conversionUnit());
    }

    public function testTheAreaCanBeConvertedFromAcresToTheConversionUnitOfHectares()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('Hectares');

        $this->assertNumber(1.34, $area_converter->from(3.311212, 'Acres')->toSelectedConversionUnit());
    }

    public function testTheAreaCanBeConvertedFromSquareMetresToTheConversionUnitOfAcres()
    {
        $area_converter = new AreaConverter();

        $area_converter->selectConversionUnit('Acres');

        $this->assertNumber(1.22, $area_converter->fromSquareMetres(4970)->toSelectedConversionUnit());
    }
}
