<?php

namespace Diffy;

class Project {

  static $ENVIRONMENTS = ['prod', 'stage', 'dev', 'baseline'];

  /**
   * Get list of all Projects.
   */
  public static function all($params = []) {
    return Diffy::request('GET', 'projects');
  }

  /**
   * @param int $projectId
   *   Project ID.
   */
  public static function compare(int $projectId, $params = []) {
    if (!isset($params['env1'])) {
      throw new InvalidArgumentsException('Compare call requires "env1" as the first environment to compare.');
    }
    if (!isset($params['env2'])) {
      throw new InvalidArgumentsException('Compare call requires "env2" as the second environment to compare.');
    }
    if (!in_array($params['env1'], self::$ENVIRONMENTS)) {
      throw new InvalidArgumentsException('"env1" is not a valid environment. Can be one of: prod, stage, dev, baseline');
    }
    if (!in_array($params['env2'], self::$ENVIRONMENTS)) {
      throw new InvalidArgumentsException('"env2" is not a valid environment. Can be one of: prod, stage, dev, baseline');
    }

    return Diffy::request('POST', 'projects/' . $projectId . '/compare', [
      'env1' => $params['env1'],
      'env2' => $params['env2']
    ]);
  }

}
