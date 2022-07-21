<?php

namespace Feycot\PageAnalyzer\Schema;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

function drop()
{
    Capsule::schema()->dropAllTables();
}

function load()
{
    if (!Capsule::schema()->hasTable('urls')) {
        Capsule::schema()->create('urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamp('created_at');
        });
    }
    if (!Capsule::schema()->hasTable('url_checks')) {
        Capsule::schema()->create('url_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('url_id');
            $table->foreign('url_id')->references('id')->on('urls');
            $table->integer('status_code')->nullable();
            $table->string('h1', 1000)->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at');
        });
    }
}
