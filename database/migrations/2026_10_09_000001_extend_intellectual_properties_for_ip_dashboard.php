<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'category',
        'bil',
        'nama_produk_baru',
        'tahun',
        'tahun_aktiviti',
        'fakulti',
        'coe',
        'jawatan',
        'no_staf',
        'gelaran',
        'nama_penyelidik',
        'penyelidik_bersama',
        'geran',
        'status_produk',
        'ejen',
        'pewartaan',
        'kos',
        'abstrak',
        'kategori',
        'tarikh_notis',
        'tarikh_pewartaan',
        'tarikh_renewal_terakhir',
        'tarikh_renewal_seterusnya',
        'kos_renewal',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('intellectual_properties')) {
            return;
        }

        Schema::table('intellectual_properties', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('bil')->nullable();
            $table->string('nama_produk_baru')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->unsignedSmallInteger('tahun_aktiviti')->nullable();
            $table->string('fakulti')->nullable();
            $table->string('coe')->nullable();
            $table->string('jawatan')->nullable();
            $table->string('no_staf')->nullable();
            $table->string('gelaran')->nullable();
            $table->string('nama_penyelidik')->nullable();
            $table->text('penyelidik_bersama')->nullable();
            $table->string('geran')->nullable();
            $table->string('status_produk')->nullable();
            $table->string('ejen')->nullable();
            $table->string('pewartaan')->nullable();
            $table->decimal('kos', 12, 2)->nullable();
            $table->text('abstrak')->nullable();
            $table->string('kategori')->nullable();
            $table->date('tarikh_notis')->nullable();
            $table->date('tarikh_pewartaan')->nullable();
            $table->date('tarikh_renewal_terakhir')->nullable();
            $table->date('tarikh_renewal_seterusnya')->nullable();
            $table->decimal('kos_renewal', 12, 2)->nullable();
            $table->index('category');
            $table->index('tahun');
            $table->index('fakulti');
        });

        DB::table('intellectual_properties')
            ->whereNull('category')
            ->update(['category' => DB::raw('type')]);

        DB::table('intellectual_properties')
            ->whereNull('status_produk')
            ->update(['status_produk' => DB::raw('status')]);

        DB::table('intellectual_properties')
            ->whereNotNull('filing_date')
            ->orderBy('id')
            ->select(['id', 'filing_date'])
            ->chunkById(500, function ($records) {
                foreach ($records as $record) {
                    $year = (int) substr((string) $record->filing_date, 0, 4);
                    if ($year >= 1900 && $year <= 2100) {
                        DB::table('intellectual_properties')
                            ->where('id', $record->id)
                            ->whereNull('tahun')
                            ->update(['tahun' => $year]);
                    }
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('intellectual_properties')) {
            return;
        }

        Schema::table('intellectual_properties', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['tahun']);
            $table->dropIndex(['fakulti']);
            $table->dropColumn($this->columns);
        });
    }
};
