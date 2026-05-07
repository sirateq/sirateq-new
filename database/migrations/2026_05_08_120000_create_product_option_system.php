<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_option_groups')) {
            Schema::create('product_option_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->string('display_type', 32)->default('text');
                $table->timestamps();

                $table->index(['product_id', 'sort_order']);
            });
        }

        if (! Schema::hasTable('product_option_values')) {
            Schema::create('product_option_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_option_group_id')->constrained()->cascadeOnDelete();
                $table->string('label');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->string('hex_color', 7)->nullable();
                $table->foreignId('product_image_id')->nullable()->constrained('product_images')->nullOnDelete();
                $table->timestamps();

                $table->index(['product_option_group_id', 'sort_order']);
            });
        }

        if (! Schema::hasTable('product_variant_option_selections')) {
            Schema::create('product_variant_option_selections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_option_group_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_option_value_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_variant_id', 'product_option_group_id'], 'variant_one_value_per_group');
            });
        }

        $this->backfillFromLegacyVariantNames();
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_option_selections');
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_option_groups');
    }

    private function backfillFromLegacyVariantNames(): void
    {
        if (! Schema::hasTable('product_variants')) {
            return;
        }

        if (! Schema::hasTable('product_option_groups') || ! Schema::hasTable('product_option_values') || ! Schema::hasTable('product_variant_option_selections')) {
            return;
        }

        $variants = DB::table('product_variants')->orderBy('id')->get();

        foreach ($variants as $variant) {
            if (DB::table('product_variant_option_selections')->where('product_variant_id', $variant->id)->exists()) {
                continue;
            }

            $productId = (int) $variant->product_id;

            $groupId = DB::table('product_option_groups')
                ->where('product_id', $productId)
                ->orderBy('id')
                ->value('id');

            if (! $groupId) {
                $groupId = DB::table('product_option_groups')->insertGetId([
                    'product_id' => $productId,
                    'name' => 'Option',
                    'sort_order' => 0,
                    'display_type' => 'text',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $label = (string) $variant->name;

            $valueId = DB::table('product_option_values')
                ->where('product_option_group_id', $groupId)
                ->where('label', $label)
                ->value('id');

            if (! $valueId) {
                $maxSort = (int) DB::table('product_option_values')
                    ->where('product_option_group_id', $groupId)
                    ->max('sort_order');

                $valueId = DB::table('product_option_values')->insertGetId([
                    'product_option_group_id' => $groupId,
                    'label' => $label,
                    'sort_order' => $maxSort + 1,
                    'hex_color' => null,
                    'product_image_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('product_variant_option_selections')->insert([
                'product_variant_id' => $variant->id,
                'product_option_group_id' => $groupId,
                'product_option_value_id' => $valueId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
