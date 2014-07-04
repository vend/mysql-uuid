<?php

namespace MysqlUuid\Formats;

abstract class Reorderable implements Format
{
    /**
     * @var boolean
     */
    protected $reorder;

    /**
     * @param boolean $reorder Whether to reorder fields to obtain a more useful ID
     */
    public function __construct($reorder = true)
    {
        $this->reorder = $reorder;
    }
}
