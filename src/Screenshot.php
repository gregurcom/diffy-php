<?php

namespace Diffy;

class Screenshot {

  static $TYPES = ['production', 'staging', 'development', 'custom'];

  // Screenshots were not started.
  const NOT_STARTED = 0;
  // Actively in progress.
  const PROGRESS = 1;
  // Completed but event "completed" is not yet fired. We send notifications,
  // webhook on this event.
  const COMPLETED = 2;
  // "Completed" event is fired. Starting to create a zipfile.
  const COMPLETED_HOOK_EXECUTED = 3;
  // Zipfile is completed.
  const ZIPFILE = 4;

  /**
   * Screenshot's data.
   *
   * @var array
   */
  public $data;

  public $screenshotId;

  /**
   * Screenshot constructor.
   */
  protected function __construct(int $screenshotId) {
    $this->screenshotId = $screenshotId;
  }

  /**
   * Create set of Screenshots.
   *
   * @param \Diffy\int $projectId
   * @param \Diffy\string $environment
   * @return mixed
   * @throws \Diffy\InvalidArgumentsException
   */
  public static function create(int $projectId, string $environment) {
    if (empty($projectId)) {
      throw new InvalidArgumentsException('Project ID can not be empty');
    }
    if (!in_array($environment, self::$TYPES)) {
      throw new InvalidArgumentsException('"' . $environment . '" is not a valid environment. Can be one of: production, staging, development, custom');
    }

    return Diffy::request('POST', 'projects/' . $projectId . '/screenshots', [
      'environment' => $environment,
    ]);
  }

  /**
   * Set whole set of screenshots as a Baseline.
   *
   * @param \Diffy\int $projectId
   * @param \Diffy\int $screenshotId
   * @return mixed
   */
  public static function setBaselineSet(int $projectId, int $screenshotId) {
    return Diffy::request('PUT', 'projects/' . $projectId . '/set-base-line-set/' . $screenshotId);
  }

  /**
   * Load full info on Screenshot.
   *
   * @param \Diffy\int $screenshotId
   * @return mixed
   */
  public static function retrieve(int $screenshotId) {
    $instance = new Screenshot($screenshotId);
    $instance->refresh();
    return $instance;
  }

  /**
   * Refresh data about current Screenshot.
   */
  public function refresh() {
    $this->data = Diffy::request('GET', 'snapshots/' . $this->screenshotId);
  }

  /**
   * Check if Screenshot is completed.
   *
   * @return boolean
   */
  public function isCompleted() {
    return in_array($this->data['state'], [self::STATUS_COMPLETED, self::STATUS_ZIPFILE]);
  }

}
