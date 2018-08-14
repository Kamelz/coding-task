<?php

namespace Src\Models;

use Src\DB;

/**
 * Class CoachStudent
 *
 * @package Src\Models
 */
class CoachStudent
{
    const TABLE = 'coaches_students';

    /**
     *
     */
    public static function emptyTable(){
        DB::truncate(self::TABLE);
    }

}