<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license proprietary
 * @link https://buzzingpixel.com/software/ansel-craft
 */

namespace buzzingpixel\ansel\services;

use buzzingpixel\ansel\models\AnselImageModel;

/**
 * Class AnselImageServiceLivePreview
 */
class AnselImageServiceLivePreview extends AnselImageService
{
    /** @var array $fieldArray */
    private $fieldArray = [];

    /**
     * Sets the field array
     * @param array $fieldArray
     * @return self
     */
    public function setFieldArray(array $fieldArray) : self
    {
        $this->fieldArray = $fieldArray;
        return $this;
    }

    /**
     * Counts all results (unlimited) specified by params on object
     * @return int
     */
    public function count() : int
    {
        return \count($this->populateQuery());
    }

    /**
     * Gets all results based on criteria set on object
     * @return AnselImageModel[]
     * @throws \Exception
     */
    public function all() : array
    {
        $items = $this->populateQuery();

        if ($this->offset) {
            $items = \array_slice($items, $this->offset);
        }

        $count = 1;
        if ($this->limit) {
            foreach ($items as $key => $model) {
                if ($count > $this->limit) {
                    unset($items[$key]);
                }

                $count++;
            }
        }

        if ($this->random) {
            shuffle($items);
        }

        return $items;
    }

    /**
     * @return array
     */
    private function populateQuery() : array
    {
        $newArray = $this->fieldArray;

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

            foreach ($newArray as $key => $model) {
                if ($op === '=') {
                    if ($model->{$param} != $compare) {
                        unset($newArray[$key]);
                    }
                } elseif ($op === '!=') {
                    if ($model->{$param} == $compare) {
                        unset($newArray[$key]);
                    }
                } elseif ($op === '>') {
                    if ($model->{$param} <= $compare) {
                        unset($newArray[$key]);
                    }
                } elseif ($op === '>=') {
                    if ($model->{$param} < $compare) {
                        unset($newArray[$key]);
                    }
                } elseif ($op === '<') {
                    if ($model->{$param} >= $compare) {
                        unset($newArray[$key]);
                    }
                } elseif ($op === '<=') {
                    if ($model->{$param} > $compare) {
                        unset($newArray[$key]);
                    }
                }
            }
        }

        if ($this->coverOnly) {
            foreach ($newArray as $key => $model) {
                if (! $model->cover) {
                    unset($newArray[$key]);
                }
            }
        }

        if ($this->skipCover) {
            foreach ($newArray as $key => $model) {
                if ($model->cover) {
                    unset($newArray[$key]);
                }
            }
        }

        if (! $this->showDisabled) {
            foreach ($newArray as $key => $model) {
                if ($model->disabled) {
                    unset($newArray[$key]);
                }
            }
        }

        return array_values($newArray);
    }
}
