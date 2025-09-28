<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObjectivesActionsAndContactToCampaignsTable extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->json('objectives')->nullable()->after('content');
            $table->json('actions')->nullable()->after('objectives');
            $table->text('contact_info')->nullable()->after('actions');
        });
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['objectives', 'actions', 'contact_info']);
        });
    }
}
