<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('providers', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('fcm_tokens', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('providers', function(Blueprint $table) {
			$table->foreign('category_id')->references('id')->on('categories')
						->onDelete('set null')
						->onUpdate('set null');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->foreign('provider_id')->references('id')->on('providers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('reports', function(Blueprint $table) {
			$table->foreign('reporter_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('day_provider', function(Blueprint $table) {
			$table->foreign('provider_id')->references('id')->on('providers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('day_provider', function(Blueprint $table) {
			$table->foreign('day_id')->references('id')->on('days')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('cars', function(Blueprint $table) {
			$table->foreign('brand_model_id')->references('id')->on('brand_models')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('cars', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('brand_models', function(Blueprint $table) {
			$table->foreign('brand_id')->references('id')->on('brands')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('users', function(Blueprint $table) {
			$table->foreign('city_id')->references('id')->on('cities')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('providers', function(Blueprint $table) {
			$table->foreign('city_id')->references('id')->on('cities')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		
		Schema::table('requests', function(Blueprint $table) {
			$table->foreign('city_id')->references('id')->on('cities')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('requests', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('offers', function(Blueprint $table) {
			$table->foreign('provider_id')->references('id')->on('providers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('offers', function(Blueprint $table) {
			$table->foreign('city_id')->references('id')->on('cities')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('offers', function(Blueprint $table) {
			$table->foreign('request_id')->references('id')->on('requests')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->foreign('provider_id')->references('id')->on('providers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->foreign('package_id')->references('id')->on('packages')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('banners', function(Blueprint $table) {
			$table->foreign('provider_id')->references('id')->on('providers')
						->onDelete('restrict')
						->onUpdate('restrict');
		});

	}

	public function down()
	{
		Schema::table('providers', function(Blueprint $table) {
			$table->dropForeign('providers_user_id_foreign');
		});
		Schema::table('providers', function(Blueprint $table) {
			$table->dropForeign('providers_category_id_foreign');
		});
		Schema::table('products', function(Blueprint $table) {
			$table->dropForeign('products_provider_id_foreign');
		});
		Schema::table('reports', function(Blueprint $table) {
			$table->dropForeign('reports_reporter_id_foreign');
		});
		Schema::table('day_provider', function(Blueprint $table) {
			$table->dropForeign('day_provider_provider_id_foreign');
		});
		Schema::table('day_provider', function(Blueprint $table) {
			$table->dropForeign('day_provider_day_id_foreign');
		});
		Schema::table('comments', function(Blueprint $table) {
			$table->dropForeign('comments_parent_id_foreign');
		});
		Schema::table('cars', function(Blueprint $table) {
			$table->dropForeign('cars_brand_model_id_foreign');
		});
		Schema::table('cars', function(Blueprint $table) {
			$table->dropForeign('cars_user_id_foreign');
		});
		Schema::table('brand_models', function(Blueprint $table) {
			$table->dropForeign('brand_models_brand_id_foreign');
		});
		Schema::table('requests', function(Blueprint $table) {
			$table->dropForeign('requests_city_id_foreign');
		});
		Schema::table('requests', function(Blueprint $table) {
			$table->dropForeign('requests_user_id_foreign');
		});
		Schema::table('offers', function(Blueprint $table) {
			$table->dropForeign('offers_provider_id_foreign');
		});
		Schema::table('offers', function(Blueprint $table) {
			$table->dropForeign('offers_city_id_foreign');
		});
		Schema::table('offers', function(Blueprint $table) {
			$table->dropForeign('offers_request_id_foreign');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->dropForeign('subscriptions_provider_id_foreign');
		});
		Schema::table('subscriptions', function(Blueprint $table) {
			$table->dropForeign('subscriptions_package_id_foreign');
		});
		Schema::table('banners', function(Blueprint $table) {
			$table->dropForeign('banners_provider_id_foreign');
		});

	}
}
