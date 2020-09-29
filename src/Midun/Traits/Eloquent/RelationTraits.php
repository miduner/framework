<?php

namespace Midun\Traits\Eloquent;

use Midun\Eloquent\Relationship\HasOneRelation;

trait RelationTraits
{
    /**
     * Relation has one
     * 
     * @param string $model
     * @param string $localKey
     * @param string $remoteKey
     * 
     * @return HasOneRelation
     */
    public function hasOne(string $model, string $localKey = "", string $remoteKey = ""): HasOneRelation
    {
        return new HasOneRelation($model, $localKey, $remoteKey, $this);
    }
}
