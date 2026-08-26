<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table          = 'gallery';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = false;
    protected $allowedFields  = ['src', 'alt', 'span', 'sort_order'];

    protected $validationRules = [
        'src'  => 'required|max_length[255]',
        'span' => 'permit_empty|greater_than_equal_to[1]|less_than_equal_to[3]',
    ];

    public function ordered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
    }
}
