<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IpSchemaMigrationTest extends TestCase
{
    public function test_legacy_ip_staff_links_are_remapped_without_id_collisions(): void
    {
        Schema::create('ip_records', function (Blueprint $table) {
            $table->id();
            foreach ([
                'category', 'bil', 'nama_produk', 'nama_produk_baru', 'tahun', 'tahun_aktiviti',
                'tarikh_difailkan', 'fakulti', 'coe', 'jawatan', 'no_staf', 'gelaran',
                'nama_penyelidik', 'penyelidik_bersama', 'geran', 'no_pemfailan', 'status_produk',
                'ejen', 'jenis_ipr', 'pewartaan', 'kos', 'abstrak', 'kategori', 'tarikh_notis',
                'tarikh_pewartaan', 'tarikh_luput',
            ] as $column) {
                $table->text($column)->nullable();
            }
            $table->timestamps();
        });
        DB::table('ip_records')->insert(['id' => 1, 'nama_produk' => 'New source record']);

        Schema::create('intellectual_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->text('title')->nullable();
            $table->string('status')->nullable();
            $table->date('filing_date')->nullable();
        });
        foreach ([1, 2, 3] as $id) {
            DB::table('intellectual_properties')->insert([
                'id' => $id,
                'type' => 'PATENT',
                'title' => 'Legacy '.$id,
            ]);
        }

        Schema::create('ip_staff', function (Blueprint $table) {
            $table->unsignedBigInteger('ip_id');
            $table->unsignedBigInteger('staff_id');
            $table->primary(['ip_id', 'staff_id']);
        });
        foreach ([1, 2, 3] as $id) {
            DB::table('ip_staff')->insert(['ip_id' => $id, 'staff_id' => 100 + $id]);
        }
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name');
        });
        DB::table('products')->insert(['id' => 7, 'product_name' => 'Legacy product']);
        Schema::create('trl_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->text('product');
        });
        DB::table('trl_products')->insert(['id' => 1, 'uuid' => 'trl-source-row', 'product' => 'TRL source product']);

        $migration = require database_path('migrations/2026_10_09_000003_move_ip_and_legacy_product_tables.php');
        $migration->up();

        $this->assertSame(4, DB::table('ip_records')->count());
        $this->assertSame(3, DB::table('legacy_intellectual_properties_archive')->count());
        $this->assertSame('Legacy product', DB::table('legacy_products_archive')->where('id', 7)->value('product_name'));
        $this->assertSame('TRL source product', DB::table('trl_products')->where('id', 1)->value('product'));
        foreach ([1 => 2, 2 => 3, 3 => 4] as $staffId => $recordId) {
            $this->assertSame(
                $recordId,
                (int) DB::table('ip_staff')->where('staff_id', 100 + $staffId)->value('ip_id')
            );
        }
    }
}
