<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LEGACY_IP_TABLE = 'legacy_intellectual_properties_archive';
    private const LEGACY_PRODUCTS_TABLE = 'legacy_products_archive';
    private const LEGACY_IP_STAFF_TABLE = 'legacy_ip_staff_archive';

    public function up(): void
    {
        if (Schema::hasTable(self::LEGACY_IP_TABLE) && Schema::hasTable('intellectual_properties')) {
            throw new RuntimeException('Both active and archived IP tables exist; resolve this before migrating.');
        }

        $this->createIpRecordsTable();
        $this->addCompatibilityColumns();

        if (Schema::hasTable('intellectual_properties')) {
            $idMap = $this->copyLegacyIpRecords();
            $this->archiveAndRetargetIpStaff($idMap);
            Schema::rename('intellectual_properties', self::LEGACY_IP_TABLE);
        }

        $this->archiveLegacyProducts();
    }

    public function down(): void
    {
        if (Schema::hasTable('ip_records') && Schema::hasTable('ip_records_archive')) {
            throw new RuntimeException('Cannot roll back IP table migration because ip_records_archive already exists.');
        }

        if (Schema::hasTable('ip_staff') && Schema::hasTable(self::LEGACY_IP_STAFF_TABLE)) {
            $this->dropIpStaffForeignKeys();
            Schema::rename('ip_staff', 'ip_records_staff_archive');
            Schema::rename(self::LEGACY_IP_STAFF_TABLE, 'ip_staff');

            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE `ip_staff` MODIFY `ip_id` INT NOT NULL');
            }
        }

        if (Schema::hasTable(self::LEGACY_IP_TABLE) && ! Schema::hasTable('intellectual_properties')) {
            Schema::rename(self::LEGACY_IP_TABLE, 'intellectual_properties');
        }

        if (DB::connection()->getDriverName() === 'mysql' && Schema::hasTable('ip_staff')) {
            $this->addForeignKey('ip_staff', 'ip_id', 'intellectual_properties', 'ip_staff_ip_fk');
        }

        if (Schema::hasTable('ip_records')) {
            Schema::rename('ip_records', 'ip_records_archive');
        }

        if (Schema::hasTable(self::LEGACY_PRODUCTS_TABLE) && ! Schema::hasTable('products')) {
            Schema::rename(self::LEGACY_PRODUCTS_TABLE, 'products');
        }
    }

    private function createIpRecordsTable(): void
    {
        if (Schema::hasTable('ip_records')) {
            return;
        }

        Schema::create('ip_records', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable()->index();
            $table->string('bil')->nullable();
            $table->text('nama_produk')->nullable();
            $table->string('nama_produk_baru')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable()->index();
            $table->unsignedSmallInteger('tahun_aktiviti')->nullable();
            $table->date('tarikh_difailkan')->nullable();
            $table->string('fakulti')->nullable()->index();
            $table->string('coe')->nullable();
            $table->string('jawatan')->nullable();
            $table->string('no_staf')->nullable();
            $table->string('gelaran')->nullable();
            $table->string('nama_penyelidik')->nullable();
            $table->text('penyelidik_bersama')->nullable();
            $table->string('geran')->nullable();
            $table->string('no_pemfailan')->nullable();
            $table->string('status_produk')->nullable();
            $table->string('ejen')->nullable();
            $table->string('jenis_ipr')->nullable();
            $table->string('pewartaan')->nullable();
            $table->decimal('kos', 12, 2)->nullable();
            $table->text('abstrak')->nullable();
            $table->string('kategori')->nullable();
            $table->date('tarikh_notis')->nullable();
            $table->date('tarikh_pewartaan')->nullable();
            $table->date('tarikh_luput')->nullable();

            $table->string('ip_number')->nullable();
            $table->text('title')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->date('filing_date')->nullable();
            $table->date('grant_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('country')->nullable();
            $table->string('link_to_evidence')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('project_id')->nullable();
            $table->date('tarikh_renewal_terakhir')->nullable();
            $table->date('tarikh_renewal_seterusnya')->nullable();
            $table->decimal('kos_renewal', 12, 2)->nullable();
            $table->unsignedBigInteger('legacy_ip_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    private function addCompatibilityColumns(): void
    {
        $columns = [
            'ip_number' => fn (Blueprint $table) => $table->string('ip_number')->nullable(),
            'title' => fn (Blueprint $table) => $table->text('title')->nullable(),
            'type' => fn (Blueprint $table) => $table->string('type')->nullable(),
            'status' => fn (Blueprint $table) => $table->string('status')->nullable(),
            'filing_date' => fn (Blueprint $table) => $table->date('filing_date')->nullable(),
            'grant_date' => fn (Blueprint $table) => $table->date('grant_date')->nullable(),
            'expiry_date' => fn (Blueprint $table) => $table->date('expiry_date')->nullable(),
            'country' => fn (Blueprint $table) => $table->string('country')->nullable(),
            'link_to_evidence' => fn (Blueprint $table) => $table->string('link_to_evidence')->nullable(),
            'remarks' => fn (Blueprint $table) => $table->text('remarks')->nullable(),
            'project_id' => fn (Blueprint $table) => $table->integer('project_id')->nullable(),
            'tarikh_renewal_terakhir' => fn (Blueprint $table) => $table->date('tarikh_renewal_terakhir')->nullable(),
            'tarikh_renewal_seterusnya' => fn (Blueprint $table) => $table->date('tarikh_renewal_seterusnya')->nullable(),
            'kos_renewal' => fn (Blueprint $table) => $table->decimal('kos_renewal', 12, 2)->nullable(),
            'legacy_ip_id' => fn (Blueprint $table) => $table->unsignedBigInteger('legacy_ip_id')->nullable()->unique(),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('ip_records', $column)) {
                Schema::table('ip_records', $definition);
            }
        }
    }

    private function copyLegacyIpRecords(): array
    {
        $idMap = [];

        DB::table('intellectual_properties')
            ->orderBy('id')
            ->chunkById(500, function ($records) use (&$idMap) {
                foreach ($records as $record) {
                    $legacy = (array) $record;
                    $legacyId = (int) $legacy['id'];
                    $existingId = DB::table('ip_records')
                        ->where('legacy_ip_id', $legacyId)
                        ->value('id');

                    if ($existingId !== null) {
                        $idMap[$legacyId] = (int) $existingId;
                        continue;
                    }

                    $type = $legacy['type'] ?? null;
                    $title = $legacy['title'] ?? '';
                    $filingDate = $legacy['filing_date'] ?? null;
                    $ipNumber = $legacy['ip_number'] ?? null;
                    $status = $legacy['status'] ?? null;
                    $expiryDate = $legacy['expiry_date'] ?? null;

                    $values = [
                        'legacy_ip_id' => $legacyId,
                        'category' => $legacy['category'] ?? $type ?? 'LEGACY',
                        'bil' => $legacy['bil'] ?? null,
                        'nama_produk' => $title,
                        'nama_produk_baru' => $legacy['nama_produk_baru'] ?? null,
                        'tahun' => $legacy['tahun'] ?? ($filingDate ? (int) substr((string) $filingDate, 0, 4) : null),
                        'tahun_aktiviti' => $legacy['tahun_aktiviti'] ?? null,
                        'tarikh_difailkan' => $filingDate,
                        'fakulti' => $legacy['fakulti'] ?? null,
                        'coe' => $legacy['coe'] ?? null,
                        'jawatan' => $legacy['jawatan'] ?? null,
                        'no_staf' => $legacy['no_staf'] ?? null,
                        'gelaran' => $legacy['gelaran'] ?? null,
                        'nama_penyelidik' => $legacy['nama_penyelidik'] ?? null,
                        'penyelidik_bersama' => $legacy['penyelidik_bersama'] ?? null,
                        'geran' => $legacy['geran'] ?? null,
                        'no_pemfailan' => $ipNumber,
                        'status_produk' => $legacy['status_produk'] ?? $status,
                        'ejen' => $legacy['ejen'] ?? null,
                        'jenis_ipr' => $type,
                        'pewartaan' => $legacy['pewartaan'] ?? null,
                        'kos' => $legacy['kos'] ?? null,
                        'abstrak' => $legacy['abstrak'] ?? null,
                        'kategori' => $legacy['kategori'] ?? null,
                        'tarikh_notis' => $legacy['tarikh_notis'] ?? null,
                        'tarikh_pewartaan' => $legacy['tarikh_pewartaan'] ?? null,
                        'tarikh_luput' => $expiryDate,
                        'ip_number' => $ipNumber,
                        'title' => $title,
                        'type' => $type,
                        'status' => $status,
                        'filing_date' => $filingDate,
                        'grant_date' => $legacy['grant_date'] ?? null,
                        'expiry_date' => $expiryDate,
                        'country' => $legacy['country'] ?? null,
                        'link_to_evidence' => $legacy['link_to_evidence'] ?? null,
                        'remarks' => $legacy['remarks'] ?? null,
                        'project_id' => $legacy['project_id'] ?? null,
                        'tarikh_renewal_terakhir' => $legacy['tarikh_renewal_terakhir'] ?? null,
                        'tarikh_renewal_seterusnya' => $legacy['tarikh_renewal_seterusnya'] ?? null,
                        'kos_renewal' => $legacy['kos_renewal'] ?? null,
                        'created_at' => $legacy['created_at'] ?? now(),
                        'updated_at' => $legacy['updated_at'] ?? now(),
                    ];

                    if (! DB::table('ip_records')->where('id', $legacyId)->exists()) {
                        $values['id'] = $legacyId;
                        DB::table('ip_records')->insert($values);
                        $idMap[$legacyId] = $legacyId;
                    } else {
                        $idMap[$legacyId] = (int) DB::table('ip_records')->insertGetId($values);
                    }
                }
            });

        return $idMap;
    }

    private function archiveAndRetargetIpStaff(array $idMap): void
    {
        if (! Schema::hasTable('ip_staff')) {
            return;
        }

        if (! Schema::hasTable(self::LEGACY_IP_STAFF_TABLE)) {
            Schema::create(self::LEGACY_IP_STAFF_TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger('ip_id');
                $table->integer('staff_id');
                $table->primary(['ip_id', 'staff_id']);
            });

            DB::table('ip_staff')
                ->orderBy('ip_id')
                ->orderBy('staff_id')
                ->chunk(500, function ($rows) {
                    DB::table(self::LEGACY_IP_STAFF_TABLE)->insertOrIgnore(
                        $rows->map(fn ($row) => ['ip_id' => $row->ip_id, 'staff_id' => $row->staff_id])->all()
                    );
                });
        }

        $this->dropIpStaffForeignKeys();

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `ip_staff` MODIFY `ip_id` BIGINT UNSIGNED NOT NULL');
        }

        DB::table('ip_staff')->delete();
        DB::table(self::LEGACY_IP_STAFF_TABLE)
            ->orderBy('ip_id')
            ->orderBy('staff_id')
            ->chunk(500, function ($rows) use ($idMap) {
                $mappedRows = $rows->map(fn ($row) => [
                    'ip_id' => $idMap[(int) $row->ip_id] ?? $row->ip_id,
                    'staff_id' => $row->staff_id,
                ])->all();

                DB::table('ip_staff')->insertOrIgnore($mappedRows);
            });

        $this->addForeignKey('ip_staff', 'ip_id', 'ip_records', 'ip_staff_ip_records_fk');
    }

    private function archiveLegacyProducts(): void
    {
        if (Schema::hasTable('products') && Schema::hasTable(self::LEGACY_PRODUCTS_TABLE)) {
            throw new RuntimeException('Both products and its archive table exist; resolve this before migrating.');
        }

        if (Schema::hasTable('products')) {
            Schema::rename('products', self::LEGACY_PRODUCTS_TABLE);
        }
    }

    private function dropIpStaffForeignKeys(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $constraints = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['ip_staff', 'ip_id']
        );

        foreach ($constraints as $constraint) {
            $name = str_replace('`', '``', $constraint->CONSTRAINT_NAME);
            DB::statement("ALTER TABLE `ip_staff` DROP FOREIGN KEY `{$name}`");
        }
    }

    private function addForeignKey(string $table, string $column, string $referencedTable, string $name): void
    {
        if (DB::connection()->getDriverName() !== 'mysql' || ! Schema::hasTable($table) || ! Schema::hasTable($referencedTable)) {
            return;
        }

        $existing = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME = ?',
            [$table, $column, $referencedTable]
        );

        if ($existing !== []) {
            return;
        }

        DB::statement(
            "ALTER TABLE `{$table}` ADD CONSTRAINT `{$name}` FOREIGN KEY (`{$column}`) REFERENCES `{$referencedTable}` (`id`) ON DELETE CASCADE"
        );
    }
};
