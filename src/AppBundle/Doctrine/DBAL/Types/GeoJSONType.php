<?php

namespace AppBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Jsor\Doctrine\PostGIS\Types\GeographyType;

class GeoJSONType extends GeographyType
{
    public function getName()
    {
        return 'geojson';
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return array('geojson');
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('ST_GeographyFromText(ST_AsText(ST_GeomFromGeoJSON(%s)))', $sqlExpr);
    }

    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        // ::geometry type cast needed for 1.5
        return sprintf('ST_AsGeoJSON(%s::geometry)', $sqlExpr);
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $options = $this->getNormalizedPostGISColumnOptions($fieldDeclaration);

        return sprintf(
            '%s(%s, %d)',
            'geography',
            $options['geometry_type'],
            $options['srid']
        );
    }
}
