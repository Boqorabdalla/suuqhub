use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

if (!Schema::hasColumn('restaurant_listings', 'og_image')) {
    Schema::table('restaurant_listings', function (Blueprint $table) {
        $table->string('og_image')->nullable();
    });
}
if (!Schema::hasColumn('hotel_listings', 'og_image')) {
    Schema::table('hotel_listings', function (Blueprint $table) {
        $table->string('og_image')->nullable();
    });
}