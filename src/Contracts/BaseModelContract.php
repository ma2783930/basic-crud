<?php

namespace BasicCrud\Contracts;

use Carbon\Carbon;

abstract class BaseModelContract
{
    public int    $id;
    public string $name;
    public Carbon $created_at;
    public Carbon $updated_at;
}
