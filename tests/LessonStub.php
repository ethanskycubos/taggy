<?php

use Codecourse\Taggy\TaggableTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class LessonStub extends Eloquent
{
    use TaggableTrait;

    protected $connection = 'testbench';

    public $table = 'lessons';
}
