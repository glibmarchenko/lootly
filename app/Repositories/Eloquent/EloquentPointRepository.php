<?php

namespace App\Repositories\Eloquent;

use App\Models\Point;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\RepositoryAbstract;

class EloquentPointRepository extends RepositoryAbstract implements PointRepository
{
    public function entity()
    {
        return Point::class;
    }

    public function rollbackPoints($pointId, array $properties)
    {
        $point_record = $this->find($pointId);

        $rollback = $point_record->replicate();
        if (isset($properties['point_value'])) {
            $rollback->point_value = intval($properties['point_value']);
            unset($properties['point_value']);
        } else {
            $rollback->point_value *= -1;
        }
        $rollback->rollback = 1;

        $comment = '';
        if (isset($properties['comment'])) {
            $comment = $properties['comment'];
            unset($properties['comment']);
        }
        if (trim($comment)) {
            if (trim($rollback->title)) {
                $comment = ' ('.$comment.')';
            }
            $rollback->title .= $comment;
            $rollback->reason = $rollback->title;
        }

        $rollback->fill($properties);

        $rollback->save();

        return $rollback;
    }
}