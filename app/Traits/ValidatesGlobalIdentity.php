<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Exception;

trait ValidatesGlobalIdentity
{
    protected static function bootValidatesGlobalIdentity()
    {
        static::saving(function ($model) {
            $tables = ['admins', 'dosens', 'mahasiswas'];
            $identityColumns = ['nip', 'nim', 'nidn', 'nidk', 'nitk'];

            foreach ($identityColumns as $column) {
                if (!empty($model->$column)) {
                    foreach ($tables as $table) {
                        if ($table === $model->getTable()) {
                            $exists = DB::table($table)
                                ->where($column, $model->$column)
                                ->where('id', '!=', $model->id)
                                ->exists();
                        } else {
                            $exists = DB::table($table)
                                ->where($column, $model->$column)
                                ->exists();
                        }

                        if ($exists) {
                            throw new Exception("Identitas {$model->$column} sudah digunakan di tabel {$table}.");
                        }
                    }
                }
            }
        });
    }
}