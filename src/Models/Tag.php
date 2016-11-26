<?php

namespace Codecourse\Taggy\Models;

use Illuminate\Database\Eloquent\Model;
use Codecourse\Taggy\Scopes\TagOrderableScopesTrait;

class Tag extends Model
{
    use TagOrderableScopesTrait;
}
