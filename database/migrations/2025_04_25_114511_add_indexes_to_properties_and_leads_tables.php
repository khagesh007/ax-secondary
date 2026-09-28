<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIndexesToPropertiesAndLeadsTables extends Migration
{
    public function up()
    {
        $this->addIndexIfNotExists('properties', 'agent_id', 'properties_agent_id_index');
        $this->addIndexIfNotExists('properties', 'is_live', 'properties_is_live_index');
        $this->addIndexIfNotExists('properties', 'community_id', 'properties_community_id_index');
        $this->addIndexIfNotExists('properties', 'sub_community_id', 'properties_sub_community_id_index');
        $this->addIndexIfNotExists('leads', 'source_id', 'leads_source_id_index');
        $this->addIndexIfNotExists('leads', 'property_id', 'leads_property_id_index');
    }

    public function down()
    {
        // Remove indexes if we need to roll back
        $this->dropIndexIfExists('properties', 'properties_agent_id_index');
        $this->dropIndexIfExists('properties', 'properties_is_live_index');
        $this->dropIndexIfExists('properties', 'properties_community_id_index');
        $this->dropIndexIfExists('properties', 'properties_sub_community_id_index');
        $this->dropIndexIfExists('leads', 'leads_source_id_index');
        $this->dropIndexIfExists('leads', 'leads_property_id_index');
    }

    /**
     * Check if the index exists and add it if not.
     *
     * @param string $table
     * @param string $column
     * @param string $indexName
     */
    private function addIndexIfNotExists($table, $column, $indexName)
    {
        $indexExists = $this->indexExists($table, $indexName);
        
        if (!$indexExists) {
            Schema::table($table, function (Blueprint $table) use ($column, $indexName) {
                $table->index($column, $indexName);
            });
        }
    }

    /**
     * Check if the index exists on the given table.
     *
     * @param string $table
     * @param string $indexName
     * @return bool
     */
    private function indexExists($table, $indexName)
    {
        // Get the list of indexes for the given table
        $indexes = DB::select("SHOW INDEXES FROM {$table}");
        
        // Check if the index already exists in the list of indexes
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Drop the index if it exists.
     *
     * @param string $table
     * @param string $indexName
     */
    private function dropIndexIfExists($table, $indexName)
    {
        $indexExists = $this->indexExists($table, $indexName);

        if ($indexExists) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
}
