<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObjectivesActionsAndContactToCampaignsTable extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'objectives')) {
                $table->json('objectives')->nullable()->after('content');
            }
            if (!Schema::hasColumn('campaigns', 'actions')) {
                $table->json('actions')->nullable()->after('objectives');
            }
            if (!Schema::hasColumn('campaigns', 'contact_info')) {
                $table->text('contact_info')->nullable()->after('actions');
            }
        });
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $drops = [];
            foreach (['objectives', 'actions', 'contact_info'] as $col) {
                if (Schema::hasColumn('campaigns', $col)) {
                    $drops[] = $col;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
}
