<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('homepages')) {
            Schema::create('homepages', function (Blueprint $table) {
                $table->id();

                $table->string('about_image_1')->default('');
                $table->string('about_title')->default('');
                $table->text('about_description')->nullable();
                $table->string('about_button_text')->default('');
                $table->string('about_button_url')->default('');

                $table->string('intro_app_image_1')->default('');
                $table->string('intro_app_image_2')->default('');
                $table->string('intro_app_image_3')->default('');
                $table->string('intro_app_title')->default('');
                $table->text('intro_app_description')->nullable();
                $table->string('intro_app_button_text')->default('');
                $table->string('intro_app_button_url')->default('');

                $table->string('contact_image_1')->default('');
                $table->string('contact_title')->default('');
                $table->text('contact_description')->nullable();

                $table->string('slider_heading_1')->default('');
                $table->string('slider_heading_2')->default('');
                $table->string('slider_heading_3')->default('');
                $table->string('slider_image_1')->default('');
                $table->string('slide_image_1')->default('');
                $table->string('slide_image_2')->default('');
                $table->string('slide_image_3')->default('');
                $table->string('slide_image_4')->default('');
                $table->string('slide_image_5')->default('');
                $table->string('slide_image_6')->default('');

                $table->string('service')->default('');
                $table->string('testimonial')->default('');

                $table->text('our_clients_text')->nullable();
                $table->text('our_clients_image')->nullable();

                $table->text('our_packages_text')->nullable();
                $table->string('features')->nullable();
                $table->string('packages')->default('');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepages');
    }
};
