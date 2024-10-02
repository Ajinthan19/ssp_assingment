<?php
// Migration file: 2024_xx_xx_xxxxxx_create_rooms_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->string('name');  // Room name
            $table->integer('capacity');  // Room capacity
            $table->decimal('price_per_hour', 8, 2)->after('capacity'); // Add price_per_hour column
          //  $table->json('availability')->nullable();  // For general availability (e.g., weekdays)
            $table->json('time_slots')->nullable();  // Date-specific time slots
            $table->boolean('is_available')->default(true); // Add availability field
            $table->string('image_path')->nullable();  // Path to the room's image
            $table->timestamps();  // Timestamps for created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
