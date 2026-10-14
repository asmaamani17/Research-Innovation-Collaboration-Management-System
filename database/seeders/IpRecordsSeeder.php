<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use UnexpectedValueException;

class IpRecordsSeeder extends Seeder
{
    private const DATA_FILE = 'ip_records_seed.json';

    private const RECORD_FIELDS = [
        'category',
        'bil',
        'nama_produk',
        'nama_produk_baru',
        'tahun',
        'tahun_aktiviti',
        'tarikh_difailkan',
        'fakulti',
        'coe',
        'jawatan',
        'no_staf',
        'gelaran',
        'nama_penyelidik',
        'penyelidik_bersama',
        'geran',
        'no_pemfailan',
        'status_produk',
        'ejen',
        'jenis_ipr',
        'pewartaan',
        'kos',
        'abstrak',
        'kategori',
        'tarikh_notis',
        'tarikh_pewartaan',
        'tarikh_luput',
        'main_category',
        'secondary_category',
        'primary_application',
        'status_renewal',
        'kos_renewal',
        'tarikh_renewal_terakhir',
        'tarikh_renewal_seterusnya',
    ];

    private const DATE_FIELDS = [
        'tarikh_difailkan',
        'tarikh_notis',
        'tarikh_pewartaan',
        'tarikh_luput',
        'tarikh_renewal_terakhir',
        'tarikh_renewal_seterusnya',
    ];

    private const AMOUNT_FIELDS = ['kos', 'kos_renewal'];

    private const YEAR_FIELDS = ['tahun', 'tahun_aktiviti'];

    private const COMPATIBILITY_FIELDS = [
        'ip_number',
        'title',
        'type',
        'status',
        'filing_date',
        'expiry_date',
    ];

    private const MONTHS = [
        'januari' => 'January',
        'jan' => 'January',
        'februari' => 'February',
        'feb' => 'February',
        'mac' => 'March',
        'march' => 'March',
        'april' => 'April',
        'mei' => 'May',
        'may' => 'May',
        'jun' => 'June',
        'june' => 'June',
        'julai' => 'July',
        'jul' => 'July',
        'ogos' => 'August',
        'ogo' => 'August',
        'august' => 'August',
        'september' => 'September',
        'sept' => 'September',
        'set' => 'September',
        'setember' => 'September',
        'oktober' => 'October',
        'okt' => 'October',
        'october' => 'October',
        'november' => 'November',
        'nov' => 'November',
        'disember' => 'December',
        'dis' => 'December',
        'december' => 'December',
    ];

    public function run(): void
    {
        $path = database_path('seeders/'.self::DATA_FILE);
        if (! is_file($path)) {
            throw new RuntimeException("IP seed data was not found at {$path}.");
        }

        if (! Schema::hasTable('ip_records') || ! Schema::hasColumn('ip_records', 'source_record_id')) {
            throw new RuntimeException('Run the database migrations, including the IP source metadata migration, before seeding IP records.');
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException("Unable to read IP seed data at {$path}.");
        }

        $rows = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($rows) || $rows === []) {
            throw new RuntimeException('IP seed data must contain a non-empty JSON array of records.');
        }

        $records = [];
        $ignoredDates = 0;
        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                throw new UnexpectedValueException('IP seed row '.($index + 1).' is not an object.');
            }

            $sourceId = trim((string) ($row['id'] ?? ''));
            if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $sourceId)) {
                throw new UnexpectedValueException('IP seed row '.($index + 1).' has an invalid source ID.');
            }

            if (trim((string) ($row['nama_produk'] ?? '')) === '') {
                throw new UnexpectedValueException("IP seed row {$sourceId} has no product name.");
            }

            $record = ['source_record_id' => $sourceId];
            foreach (self::RECORD_FIELDS as $field) {
                $record[$field] = $this->normalizeValue($row[$field] ?? null);
            }

            $record['title'] = $record['nama_produk'];
            $record['ip_number'] = $record['no_pemfailan'];
            $record['type'] = $record['jenis_ipr'] ?? $record['category'] ?? 'PATENT';
            $record['status'] = $record['status_produk'];

            foreach (self::DATE_FIELDS as $field) {
                $original = $record[$field];
                $record[$field] = $this->normalizeDate($original, $sourceId, $field);
                if ($original !== null && $record[$field] === null) {
                    $ignoredDates++;
                }
            }

            $record['filing_date'] = $record['tarikh_difailkan'];
            $record['expiry_date'] = $record['tarikh_luput'];

            foreach (self::YEAR_FIELDS as $field) {
                if ($record[$field] !== null) {
                    if (! is_numeric($record[$field])) {
                        throw new UnexpectedValueException("Invalid {$field} in IP seed record {$sourceId}.");
                    }
                    $record[$field] = (int) $record[$field];
                }
            }

            foreach (self::AMOUNT_FIELDS as $field) {
                if ($record[$field] !== null) {
                    $amount = str_replace(['RM', ',', ' ', "\u{00A0}"], '', (string) $record[$field]);
                    if (! is_numeric($amount)) {
                        throw new UnexpectedValueException("Invalid {$field} in IP seed record {$sourceId}.");
                    }
                    $record[$field] = $amount;
                }
            }

            $record['created_at'] = $this->normalizeTimestamp($row['created_at'] ?? null);
            $record['updated_at'] = $this->normalizeTimestamp($row['updated_at'] ?? null);
            $records[] = $record;
        }

        $updateFields = array_merge(
            self::RECORD_FIELDS,
            self::COMPATIBILITY_FIELDS,
            ['created_at', 'updated_at']
        );

        DB::transaction(function () use ($records, $updateFields): void {
            foreach (array_chunk($records, 100) as $batch) {
                DB::table('ip_records')->upsert($batch, ['source_record_id'], $updateFields);
            }
        });

        $this->command?->info('Seeded '.count($records).' IP records from the Google Sheet export.');
        if ($ignoredDates > 0) {
            $this->command?->warn("{$ignoredDates} year-only date value(s) were stored as null because they do not specify a complete date.");
        }
    }

    private function normalizeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(str_replace("\u{00A0}", ' ', (string) $value));

        return $value === '' || $value === '-' ? null : $value;
    }

    private function normalizeDate(?string $value, string $sourceId, string $field): ?string
    {
        if ($value === null) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $value)) {
            try {
                return Carbon::parse($value)->toDateString();
            } catch (\Throwable $exception) {
                throw new UnexpectedValueException("Invalid date in IP seed record {$sourceId}, field {$field}: {$value}.", 0, $exception);
            }
        }

        if (preg_match('/^\d{4}$/', $value)) {
            return null;
        }

        $value = str_replace(["\u{00A0}", "\u{202F}", '-', '–'], [' ', ' ', ' ', ' '], $value);
        $value = preg_replace_callback('/(?<!\pL)([\pL]+)(?!\pL)/u', function (array $matches): string {
            $month = self::MONTHS[mb_strtolower($matches[1])] ?? null;

            return $month ?? $matches[1];
        }, $value);

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $exception) {
            throw new UnexpectedValueException("Invalid date in IP seed record {$sourceId}, field {$field}: {$value}.", 0, $exception);
        }
    }

    private function normalizeTimestamp(mixed $value): string
    {
        if ($value === null || trim((string) $value) === '') {
            return now()->toDateTimeString();
        }

        return Carbon::parse((string) $value)->toDateTimeString();
    }
}
