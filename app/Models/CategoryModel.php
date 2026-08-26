<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table          = 'categories';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = false;
    protected $allowedFields  = ['name', 'slug', 'blurb', 'image', 'sort_order'];

    protected $validationRules = [
        // Required so is_unique's {id} placeholder resolves on update.
        'id'   => 'permit_empty|is_natural_no_zero',
        'name' => 'required|min_length[2]|max_length[100]',
        'slug' => 'required|max_length[120]|is_unique[categories.slug,id,{id}]',
    ];

    public function ordered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->findAll();
    }
}
