<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrationSchema
{
    public static function dropForeignIfExists(string $table, string $column): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $constraintName = "{$table}_{$column}_foreign";

        $exists = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_TYPE = ?
               AND CONSTRAINT_NAME = ?',
            [$table, 'FOREIGN KEY', $constraintName]
        );

        if (!$exists) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropForeign([$column]);
        });
    }

    /**
     * Drop FK + column if present, recreate column, then add FK when reference table exists.
     */
    public static function addColumnWithForeign(
        string $table,
        string $column,
        callable $columnDefinition,
        string $referenceTable,
        string $onUpdate = 'RESTRICT',
        string $onDelete = 'CASCADE'
    ): void {
        if (!Schema::hasTable($table)) {
            return;
        }

        static::dropForeignIfExists($table, $column);

        if (Schema::hasColumn($table, $column)) {
            Schema::table($table, function (Blueprint $blueprint) use ($column) {
                $blueprint->dropColumn($column);
            });
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columnDefinition) {
            $columnDefinition($blueprint);
        });

        static::ensureForeign($table, $column, $referenceTable, $onUpdate, $onDelete);
    }

    public static function addColumnIfMissing(string $table, string $column, callable $columnDefinition): void
    {
        if (!Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columnDefinition) {
            $columnDefinition($blueprint);
        });
    }

    public static function recreateColumn(string $table, string $column, callable $columnDefinition): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        static::dropForeignIfExists($table, $column);

        if (Schema::hasColumn($table, $column)) {
            Schema::table($table, function (Blueprint $blueprint) use ($column) {
                $blueprint->dropColumn($column);
            });
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columnDefinition) {
            $columnDefinition($blueprint);
        });
    }

    public static function ensureForeign(
        string $table,
        string $column,
        string $referenceTable,
        string $onUpdate = 'RESTRICT',
        string $onDelete = 'CASCADE'
    ): void {
        if (!Schema::hasTable($table) || !Schema::hasTable($referenceTable)) {
            return;
        }

        if (!Schema::hasColumn($table, $column)) {
            return;
        }

        static::dropForeignIfExists($table, $column);

        Schema::table($table, function (Blueprint $blueprint) use (
            $column,
            $referenceTable,
            $onUpdate,
            $onDelete
        ) {
            $blueprint->foreign($column)
                ->references('id')
                ->on($referenceTable)
                ->onUpdate($onUpdate)
                ->onDelete($onDelete);
        });
    }
}
