<?php

namespace App\Migrations;

use Illuminate\Database\Migrations\MigrationCreator as BaseMigrator;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator extends BaseMigrator
{
    public function __construct(Filesystem $file, $customStubPath = null)
    {
        parent::__construct($file, $customStubPath);
    }

//    public function stubPath()
//    {
//        return __DIR__ . '/stubs';
//    }
}
