<?php
/**
 * Script to update all migration files with proper schema
 * Run this file with: php update_migrations.php
 */

$migrationsPath = __DIR__ . '/database/migrations/';

// Migration files with their content
$migrations = [
    '2025_11_04_054530_create_personal_details_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'personal_details\', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(\'registration_id\');
            $table->string(\'father_name\', 255);
            $table->string(\'mother_name\', 255);
            $table->date(\'dob\');
            $table->enum(\'gender\', [\'Male\', \'Female\', \'Other\']);
            $table->string(\'occupation\', 100);
            $table->enum(\'marital_status\', [\'Single\', \'Married\', \'Divorced\', \'Widowed\']);
            $table->string(\'pan_no\', 20);
            $table->string(\'pan_name\', 255);
            $table->string(\'aadhaar_number\', 20);
            $table->string(\'residential_status\', 50);
            $table->string(\'annual_income\', 50);
            $table->timestamp(\'added_on\')->useCurrent();
            $table->tinyInteger(\'status\')->default(1);

            $table->foreign(\'registration_id\')->references(\'id\')->on(\'registration\')->onDelete(\'cascade\');
            $table->index(\'registration_id\');
            $table->index(\'pan_no\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'personal_details\');
    }
};
',

    '2025_11_04_054530_create_address_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'address\', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(\'registration_id\');
            $table->text(\'permanent_address\');
            $table->string(\'permanent_address1\', 255)->nullable();
            $table->string(\'permanent_address2\', 255)->nullable();
            $table->string(\'permanent_address_city\', 100);
            $table->string(\'permanent_address_country\', 100);
            $table->string(\'permanent_address_pincode\', 10);
            $table->tinyInteger(\'is_same\')->default(0)->comment(\'Same as permanent\');
            $table->text(\'correspondence_address\')->nullable();
            $table->string(\'correspondence_address1\', 255)->nullable();
            $table->string(\'correspondence_address2\', 255)->nullable();
            $table->string(\'correspondence_address_city\', 100)->nullable();
            $table->string(\'correspondence_address_country\', 100)->nullable();
            $table->string(\'correspondence_address_pincode\', 10)->nullable();
            $table->timestamp(\'added_on\')->useCurrent();
            $table->tinyInteger(\'status\')->default(1);

            $table->foreign(\'registration_id\')->references(\'id\')->on(\'registration\')->onDelete(\'cascade\');
            $table->index(\'registration_id\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'address\');
    }
};
',

    '2025_11_04_054531_create_bank_details_table.php' => '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'bank_details\', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(\'registration_id\');
            $table->string(\'ifsc_code\', 20);
            $table->string(\'account_number\', 30);
            $table->enum(\'account_type\', [\'Savings\', \'Current\', \'Other\']);
            $table->string(\'bank\', 255);
            $table->string(\'branch\', 255);
            $table->text(\'address\');
            $table->string(\'micr\', 20)->nullable();
            $table->string(\'name_at_bank\', 255);
            $table->timestamp(\'added_on\')->useCurrent();
            $table->tinyInteger(\'status\')->default(1);

            $table->foreign(\'registration_id\')->references(\'id\')->on(\'registration\')->onDelete(\'cascade\');
            $table->index(\'registration_id\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'bank_details\');
    }
};
',

];

foreach ($migrations as $filename => $content) {
    $filepath = $migrationsPath . $filename;
    if (file_exists($filepath)) {
        file_put_contents($filepath, $content);
        echo "Updated: $filename\n";
    } else {
        echo "Not found: $filename\n";
    }
}

echo "\nDone! All migrations updated.\n";
