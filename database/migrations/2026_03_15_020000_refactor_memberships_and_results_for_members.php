<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function usingSqlite(): bool
    {
        return DB::getDriverName() === 'sqlite';
    }

    public function up(): void
    {
        $this->dropForeignIfExists('club_memberships', 'club_memberships_club_id_foreign');
        $this->dropForeignIfExists('club_memberships', 'club_memberships_user_id_foreign');
        $this->dropIndexIfExists('club_memberships', 'club_memberships_club_id_user_id_unique');
        $this->dropIndexIfExists('club_memberships', 'club_memberships_user_id_foreign');

        if (Schema::hasColumn('club_memberships', 'user_id') && ! Schema::hasColumn('club_memberships', 'member_id')) {
            Schema::table('club_memberships', function (Blueprint $table): void {
                $table->renameColumn('user_id', 'member_id');
            });
        }

        $this->addUniqueIfMissing('club_memberships', 'club_memberships_club_id_member_id_unique', ['club_id', 'member_id']);
        $this->addForeignIfMissing('club_memberships', 'club_memberships_club_id_foreign', 'club_id', 'clubs', 'id', 'cascade', 'cascade');
        $this->addForeignIfMissing('club_memberships', 'club_memberships_member_id_foreign', 'member_id', 'users', 'id', 'cascade', 'cascade');

        $this->dropForeignIfExists('club_renewal_requests', 'club_renewal_requests_user_id_foreign');
        $this->dropIndexIfExists('club_renewal_requests', 'club_renewal_requests_user_id_foreign');

        if (Schema::hasColumn('club_renewal_requests', 'user_id') && ! Schema::hasColumn('club_renewal_requests', 'member_id')) {
            Schema::table('club_renewal_requests', function (Blueprint $table): void {
                $table->renameColumn('user_id', 'member_id');
            });
        }

        $this->addForeignIfMissing('club_renewal_requests', 'club_renewal_requests_member_id_foreign', 'member_id', 'users', 'id', 'cascade', 'cascade');

        $this->dropForeignIfExists('training_results', 'training_results_user_id_foreign');
        $this->dropIndexIfExists('training_results', 'training_results_user_id_performed_on_index');

        if (Schema::hasColumn('training_results', 'user_id') && ! Schema::hasColumn('training_results', 'member_id')) {
            Schema::table('training_results', function (Blueprint $table): void {
                $table->renameColumn('user_id', 'member_id');
            });
        }

        $this->addForeignIfMissing('training_results', 'training_results_member_id_foreign', 'member_id', 'users', 'id', 'cascade', 'cascade');

        if (! Schema::hasColumn('training_results', 'club_id')) {
            Schema::table('training_results', function (Blueprint $table): void {
                $table->foreignId('club_id')->nullable()->after('member_id')->constrained()->nullOnDelete();
            });
        }

        DB::table('training_results')->orderBy('id')->chunkById(100, function ($results): void {
            foreach ($results as $result) {
                $clubId = DB::table('users')
                    ->where('id', $result->member_id)
                    ->value('main_club_id');

                if ($clubId === null) {
                    $clubId = DB::table('club_memberships')
                        ->where('member_id', $result->member_id)
                        ->orderBy('joined_on')
                        ->orderBy('club_id')
                        ->value('club_id');
                }

                DB::table('training_results')
                    ->where('id', $result->id)
                    ->update(['club_id' => $clubId]);
            }
        });

        $this->addIndexIfMissing('training_results', 'training_results_club_member_performed_on_index', ['club_id', 'member_id', 'performed_on']);
    }

    public function down(): void
    {
        $this->dropIndexIfExists('training_results', 'training_results_club_member_performed_on_index');
        $this->dropForeignIfExists('training_results', 'training_results_club_id_foreign');
        $this->dropForeignIfExists('training_results', 'training_results_member_id_foreign');

        if (Schema::hasColumn('training_results', 'club_id')) {
            Schema::table('training_results', function (Blueprint $table): void {
                $table->dropColumn('club_id');
            });
        }

        if (Schema::hasColumn('training_results', 'member_id') && ! Schema::hasColumn('training_results', 'user_id')) {
            Schema::table('training_results', function (Blueprint $table): void {
                $table->renameColumn('member_id', 'user_id');
            });
        }

        $this->addForeignIfMissing('training_results', 'training_results_user_id_foreign', 'user_id', 'users', 'id', 'cascade', 'cascade');
        $this->addIndexIfMissing('training_results', 'training_results_user_id_performed_on_index', ['user_id', 'performed_on']);

        $this->dropForeignIfExists('club_renewal_requests', 'club_renewal_requests_member_id_foreign');

        if (Schema::hasColumn('club_renewal_requests', 'member_id') && ! Schema::hasColumn('club_renewal_requests', 'user_id')) {
            Schema::table('club_renewal_requests', function (Blueprint $table): void {
                $table->renameColumn('member_id', 'user_id');
            });
        }

        $this->addForeignIfMissing('club_renewal_requests', 'club_renewal_requests_user_id_foreign', 'user_id', 'users', 'id', 'cascade', 'cascade');

        $this->dropForeignIfExists('club_memberships', 'club_memberships_club_id_foreign');
        $this->dropForeignIfExists('club_memberships', 'club_memberships_member_id_foreign');
        $this->dropUniqueIfExists('club_memberships', 'club_memberships_club_id_member_id_unique');

        if (Schema::hasColumn('club_memberships', 'member_id') && ! Schema::hasColumn('club_memberships', 'user_id')) {
            Schema::table('club_memberships', function (Blueprint $table): void {
                $table->renameColumn('member_id', 'user_id');
            });
        }

        $this->addUniqueIfMissing('club_memberships', 'club_memberships_club_id_user_id_unique', ['club_id', 'user_id']);
        $this->addForeignIfMissing('club_memberships', 'club_memberships_club_id_foreign', 'club_id', 'clubs', 'id', 'cascade', 'cascade');
        $this->addForeignIfMissing('club_memberships', 'club_memberships_user_id_foreign', 'user_id', 'users', 'id', 'cascade', 'cascade');
    }

    private function addForeignIfMissing(
        string $table,
        string $constraint,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $onDelete,
        string $onUpdate,
    ): void {
        if ($this->usingSqlite()) {
            return;
        }

        if ($this->hasForeignKey($table, $constraint)) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE %s ON UPDATE %s',
            $table,
            $constraint,
            $column,
            $referencedTable,
            $referencedColumn,
            strtoupper($onDelete),
            strtoupper($onUpdate),
        ));
    }

    private function addIndexIfMissing(string $table, string $index, array $columns): void
    {
        if ($this->hasIndex($table, $index)) {
            return;
        }

        if ($this->usingSqlite()) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $index): void {
                $blueprint->index($columns, $index);
            });

            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE `%s` ADD INDEX `%s` (%s)',
            $table,
            $index,
            $this->quotedColumns($columns),
        ));
    }

    private function addUniqueIfMissing(string $table, string $index, array $columns): void
    {
        if ($this->hasIndex($table, $index)) {
            return;
        }

        if ($this->usingSqlite()) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $index): void {
                $blueprint->unique($columns, $index);
            });

            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE `%s` ADD UNIQUE `%s` (%s)',
            $table,
            $index,
            $this->quotedColumns($columns),
        ));
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        if ($this->usingSqlite()) {
            return;
        }

        if (! $this->hasForeignKey($table, $constraint)) {
            return;
        }

        DB::statement(sprintf('ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $table, $constraint));
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (! $this->hasIndex($table, $index)) {
            return;
        }

        if ($this->usingSqlite()) {
            Schema::table($table, function (Blueprint $blueprint) use ($index): void {
                $blueprint->dropIndex($index);
            });

            return;
        }

        DB::statement(sprintf('ALTER TABLE `%s` DROP INDEX `%s`', $table, $index));
    }

    private function dropUniqueIfExists(string $table, string $index): void
    {
        $this->dropIndexIfExists($table, $index);
    }

    private function hasForeignKey(string $table, string $constraint): bool
    {
        if ($this->usingSqlite()) {
            return false;
        }

        return DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ? LIMIT 1',
            [$table, $constraint, 'FOREIGN KEY'],
        ) !== null;
    }

    private function hasIndex(string $table, string $index): bool
    {
        if ($this->usingSqlite()) {
            return collect(DB::select(sprintf('PRAGMA index_list(%s)', DB::getPdo()->quote($table))))
                ->contains(fn (object $item): bool => ($item->name ?? null) === $index);
        }

        return DB::selectOne(
            'SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$table, $index],
        ) !== null;
    }

    private function quotedColumns(array $columns): string
    {
        return collect($columns)
            ->map(fn (string $column): string => sprintf('`%s`', $column))
            ->implode(', ');
    }
};