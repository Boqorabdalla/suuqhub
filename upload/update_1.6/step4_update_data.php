use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

    if (!Schema::hasColumn('users', 'can_create_blog')) {
        Schema::table('users', function (Blueprint $table) {
            $table->string('can_create_blog')->nullable();
        });
    }