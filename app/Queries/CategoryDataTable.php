<?php

namespace App\Queries;

use App\Models\Category;

/**
 * Class CategoryDataTable
 */
class CategoryDataTable
{
    /**
     * @return Category
     */
    public function get()
    {
        /** @var Category $query */
        $query = Category::withCount('ticket')->get(['id', 'name', 'ticket_count', 'color']);

        return $query;
    }
}
