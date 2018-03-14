<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\services;

use craft\db\Query;
use buzzingpixel\ansel\models\AnselImageModel;

/**
 * Class AnselImageService
 */
class AnselImageService
{
    /** @var Query $query */
    private $query;

    /** @var AnselImageModel $anselImageModel */
    protected $anselImageModel;

    /** @var int $limit */
    protected $limit;

    /** @var int $offset */
    protected $offset = 0;

    /** @var string $order */
    protected $order;

    /** @var bool $random */
    protected $random = false;

    /** @var bool $coverOnly */
    protected $coverOnly = false;

    /** @var bool $skipCover */
    protected $skipCover = false;

    /** @var bool $showDisabled */
    protected $showDisabled = false;

    protected $includeArrayParams = [
        'id' => [],
        'elementId' => [],
        'fieldId' => [],
        'userId' => [],
        'assetId' => [],
        'originalAssetId' => [],
        'title' => null,
        'caption' => null,
    ];

    protected $excludeArrayParams = [
        'id' => [],
        'elementId' => [],
        'fieldId' => [],
        'userId' => [],
        'assetId' => [],
        'originalAssetId' => [],
        'title' => null,
        'caption' => null,
    ];

    /** @var array $queryParams */
    protected $comparisonOperatorParams = [
        'width' => null,
        'height' => null,
        'position' => null,
    ];

    /**
     * AnselImageService constructor
     * @param Query $query
     * @param AnselImageModel $anselImageModel
     */
    public function __construct(
        Query $query,
        AnselImageModel $anselImageModel
    ) {
        $this->query = $query;
        $this->anselImageModel = $anselImageModel;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function id($val) : self
    {
        $this->includeArrayParams['id'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function notId($val) : self
    {
        $this->excludeArrayParams['id'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function elementId($val) : self
    {
        $this->includeArrayParams['elementId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function notElementId($val) : self
    {
        $this->excludeArrayParams['elementId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function fieldId($val) : self
    {
        $this->includeArrayParams['fieldId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function notFieldId($val) : self
    {
        $this->excludeArrayParams['fieldId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function userId($val) : self
    {
        $this->includeArrayParams['userId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function notUserId($val) : self
    {
        $this->excludeArrayParams['userId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function assetId($val) : self
    {
        $this->includeArrayParams['assetId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function notAssetId($val) : self
    {
        $this->excludeArrayParams['assetId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function originalAssetId($val) : self
    {
        $this->includeArrayParams['originalAssetId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function notOriginalAssetId($val) : self
    {
        $this->excludeArrayParams['originalAssetId'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function title($val) : self
    {
        $this->includeArrayParams['title'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function notTitle($val) : self
    {
        $this->excludeArrayParams['title'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function caption($val) : self
    {
        $this->includeArrayParams['caption'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function notCaption($val) : self
    {
        $this->excludeArrayParams['caption'] = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param string
     * @return self
     */
    public function width($val) : self
    {
        $this->comparisonOperatorParams['width'] = $val;
        return $this;
    }

    /**
     * @param string
     * @return self
     */
    public function height($val) : self
    {
        $this->comparisonOperatorParams['height'] = $val;
        return $this;
    }

    /**
     * @param string
     * @return self
     */
    public function position($val) : self
    {
        $this->comparisonOperatorParams['position'] = $val;
        return $this;
    }

    /**
     * @param int $val
     * @return self
     */
    public function limit(int $val = 1) : self
    {
        $this->limit = $val;
        return $this;
    }

    /**
     * @param int $val
     * @return self
     */
    public function offset(int $val = 0) : self
    {
        $this->offset = $val;
        return $this;
    }

    /**
     * @param string|array $val
     * @return self
     */
    public function order($val) : self
    {
        $this->order = \is_array($val) ?
            $val :
            explode(',', $val);

        return $this;
    }

    /**
     * @param bool $val
     * @return self
     */
    public function random(bool $val = true) : self
    {
        $this->random = $val === true;
        return $this;
    }

    /**
     * @param bool $val
     * @return self
     */
    public function coverOnly(bool $val = true) : self
    {
        $this->coverOnly = $val === true;
        return $this;
    }

    /**
     * @param bool $val
     * @return self
     */
    public function skipCover(bool $val = true) : self
    {
        $this->skipCover = $val === true;
        return $this;
    }

    /**
     * @param bool $val
     * @return self
     */
    public function showDisabled(bool $val = true) : self
    {
        $this->showDisabled = $val === true;
        return $this;
    }

    /**
     * Counts all results (unlimited) specified by params on object
     * @return int
     */
    public function count() : int
    {
        $query = clone $this->query;

        $this->populateQuery($query);

        return (int) $query->count();
    }

    /**
     * Gets one result based on criteria set on object
     * @return null|AnselImageModel
     * @throws \Exception
     */
    public function one()
    {
        $oldLimit = $this->limit;

        $this->limit = 1;

        $returnVal = null;

        foreach ($this->all() as $one) {
            $returnVal = $one;
        }

        $this->limit = $oldLimit;

        return $returnVal;
    }

    /**
     * Gets all results based on criteria set on object
     * @return AnselImageModel[]
     * @throws \Exception
     */
    public function all() : array
    {
        $query = clone $this->query;

        $this->populateQuery($query);

        if ($this->limit) {
            $query->limit($this->limit);
        }

        if ($this->offset) {
            $query->offset($this->offset);
        }

        if ($this->random) {
            $query->orderBy('rand()');
        } elseif ($this->order) {
            $query->orderBy(implode(',', $this->order));
        }

        $models = [];

        foreach ($query->all() as $item) {
            $model = clone $this->anselImageModel;
            $model->setProperties($item);
            $models[] = $model;
        }

        return $models;
    }

    /**
     * Populates the query
     * @param Query $query
     */
    private function populateQuery(Query $query)
    {
        $query->select('*')
            ->from('{{%anselImages}}');

        $hasWhere = false;

        foreach ($this->includeArrayParams as $param => $value) {
            if (! $value) {
                continue;
            }

            $cond = "`{$param}` IN (:in{$param})";
            $params = [":in{$param}" => implode(',', $value)];

            if ($hasWhere) {
                $query->andWhere($cond, $params);
                continue;
            }

            $query->where($cond, $params);
            $hasWhere = true;
        }

        foreach ($this->excludeArrayParams as $param => $value) {
            if (! $value) {
                continue;
            }

            $cond = "`{$param}` NOT IN (:notIn{$param})";
            $params = [":notIn{$param}" => implode(',', $value)];

            if ($hasWhere) {
                $query->andWhere($cond, $params);
                continue;
            }

            $query->where($cond, $params);
            $hasWhere = true;
        }

        foreach ($this->comparisonOperatorParams as $param => $value) {
            if (! $value) {
                continue;
            }

            $availableOps = [
                '=',
                '!=',
                '>',
                '>=',
                '<',
                '<=',
            ];

            $op = $defaultOp = '=';
            $compare = $value;

            $value = preg_split('/\s+/', $value);

            if (isset($value[1])) {
                $op = $value[0];

                if (! \in_array($op, $availableOps, true)) {
                    $op = $defaultOp;
                }

                $compare = $value[1];
            }

            $cond = "`{$param}` {$op} :comp{$param}";
            $params = [":comp{$param}" => $compare];

            if ($hasWhere) {
                $query->andWhere($cond, $params);
                continue;
            }

            $query->where($cond, $params);
            $hasWhere = true;
        }

        if ($this->coverOnly) {
            if ($hasWhere) {
                $query->andWhere('`cover` = 1');
            } else {
                $query->where('`cover` = 1');
            }
        }

        if ($this->skipCover) {
            if ($hasWhere) {
                $query->andWhere('`cover` != 1');
            } else {
                $query->where('`cover` != 1');
            }
        }

        if (! $this->showDisabled) {
            if ($hasWhere) {
                $query->andWhere('`disabled` != 1');
            } else {
                $query->where('`disabled` != 1');
            }
        }
    }
}
