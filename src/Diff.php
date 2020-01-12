<?php

namespace Diffy;

/**
 * Class to interact with Diffs.
 */
class Diff {

  const STATUS_COMPLETED = 2;
  const STATUS_ZIPFILE = 4;

  /**
   * Diff's data.
   *
   * @var array
   */
  public $data;

  public $diffId;

  /**
   * Diff constructor.
   */
  protected function __construct(int $diffId) {
    $this->diffId = $diffId;
  }

  /**
   * Create a Diff.
   *
   * @param int $projectId
   * @param int $screenshotId1
   * @param int $screenshotId2
   * @return mixed
   * @throws \Diffy\InvalidArgumentsException
   */
  public static function create(int $projectId, int $screenshotId1, int $screenshotId2) {

    if (empty($projectId)) {
      throw new InvalidArgumentsException('Project ID can not be empty');
    }
    if (empty($screenshotId1)) {
      throw new InvalidArgumentsException('Screenshot 1 ID can not be empty');
    }
    if (empty($screenshotId2)) {
      throw new InvalidArgumentsException('Screenshot 2 ID can not be empty');
    }

    return Diffy::request('POST', 'projects/' . $projectId . '/diffs', [
      'snapshot1' => $screenshotId1,
      'snapshot2' => $screenshotId2,
    ]);
  }

  /**
   * Load full info on Diff.
   *
   * @param int $diffId
   * @return mixed
   */
  public static function retrieve(int $diffId) {
    $instance = new Diff($diffId);
    $instance->refresh();
    return $instance;
  }

  /**
   * Refresh data about current Diff.
   */
  public function refresh() {
    $this->data = Diffy::request('GET', 'diffs/' . $this->diffId);
  }

  /**
   * Check if Diff is completed.
   *
   * @return boolean
   */
  public function isCompleted() {
    return in_array($this->data['state'], [self::STATUS_COMPLETED, self::STATUS_ZIPFILE]);
  }

}
