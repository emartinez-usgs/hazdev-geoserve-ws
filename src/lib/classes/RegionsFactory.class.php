<?php

class RegionsFactory extends GeoserveFactory {

  protected static $SUPPORTED_TYPES = array(
    'fe'
  );

  /**
   * Get regions containing point.
   *
   * @param $query {RegionsQuery}
   *        query object
   * @param $callback {RegionsCallback}
   *        callback object
   * @return when callback is not null, nothing
   *         when callback is null:
   *         object of regions keyed by type
   * @throws Exception
   */
  public function get ($query, $callback = null) {
    $data = array();
    if ($query->type === null || in_array('fe', $query->type)) {
      $data['fe'] = $this->getFE($query);
    }

    if ($callback !== null) {
      $callback->onStart($query);
      $callback->onRegions($data);
      $callback->onEnd();
    } else {
      return $data;
    }
  }

  /**
   * @return {Array}
   *         An array of supported types
   */
  public function getSupportedTypes () {
    return RegionsFactory::$SUPPORTED_TYPES;
  }

  /**
   * Get FE regions
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getFE ($query) {
    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }
    // connect to database
    $db = $this->connect();

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geography' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
        ':latitude' => $query->latitude,
        ':longitude' => $query->longitude);

    $sql .= ' SELECT' .
        ' num as number' .
        ', place as name';
    if ($query->includeGeometry) {
      $sql .= ', ST_AsText(shape) as shape';
    }

    $sql .= ' FROM search, fe_view' .
        ' WHERE search.point && shape' .
        ' ORDER BY priority ASC, ST_Area(shape) ASC';

    // execute query
    $query = $db->prepare($sql);
    if (!$query->execute($params)) {
      // handle error
      $errorInfo = $db->errorInfo();
      throw new Exception($errorInfo[2]);
    } else {
      try {
        // return all regions
        return $query->fetchAll(PDO::FETCH_ASSOC);
      } finally {
        $query->closeCursor();
      }
    }
  }

}