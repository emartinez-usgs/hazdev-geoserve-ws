<?php

include_once dirname(__FILE__) . '/config.inc.php';


// configure factory
$CLASSES_DIR = $APP_DIR . '/lib/classes';

include_once $CLASSES_DIR . '/GeoserveWebService.class.php';
include_once $CLASSES_DIR . '/PlacesFactory.class.php';
include_once $CLASSES_DIR . '/RegionsFactory.class.php';
include_once $CLASSES_DIR . '/LayersFactory.class.php';

$PLACES_FACTORY = new PlacesFactory($CONFIG['DB_DSN'], $CONFIG['DB_USER'],
    $CONFIG['DB_PASS'], $CONFIG['DB_SCHEMA']);
$REGIONS_FACTORY = new RegionsFactory($CONFIG['DB_DSN'], $CONFIG['DB_USER'],
    $CONFIG['DB_PASS'], $CONFIG['DB_SCHEMA']);
$LAYERS_FACTORY = new LayersFactory($CONFIG['DB_DSN'], $CONFIG['DB_USER'],
    $CONFIG['DB_PASS'], $CONFIG['DB_SCHEMA']);

$SERVICE = new GeoserveWebService($PLACES_FACTORY, $REGIONS_FACTORY,
    $LAYERS_FACTORY);
