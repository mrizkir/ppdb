<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JenjangLinkKuesioner extends Migration
{
  /**
   * Link Google Form per jenjang. Nilai awal memakai tautan yang
   * sebelumnya tertulis di dashboard murid.
   */
  public function up()
  {
    if (!Schema::hasTable('jenjang_studi')) {
      return;
    }

    if (!Schema::hasColumn('jenjang_studi', 'link_kuesioner')) {
      Schema::table('jenjang_studi', function (Blueprint $table) {
        $table->text('link_kuesioner')->nullable()->after('status_pendaftaran');
      });
    }

    $tautan = 'https://docs.google.com/forms/d/e/1FAIpQLSd-8KQpZ_9RYRsNqSGCS4BuYVSSWZrILVGkpjZgm8Af8st70w/viewform?usp=sharing&ouid=116590670459241609195';
    \DB::table('jenjang_studi')
      ->whereNull('link_kuesioner')
      ->update(['link_kuesioner' => $tautan]);
  }

  public function down()
  {
    if (Schema::hasTable('jenjang_studi') && Schema::hasColumn('jenjang_studi', 'link_kuesioner')) {
      Schema::table('jenjang_studi', function (Blueprint $table) {
        $table->dropColumn('link_kuesioner');
      });
    }
  }
}
