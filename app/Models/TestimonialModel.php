<?php

namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table          = 'testimonials';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = false;
    protected $allowedFields  = ['name', 'stars', 'quote', 'item', 'is_published'];

    protected $validationRules = [
        'name'  => 'required|max_length[120]',
        'quote' => 'required',
        'stars' => 'permit_empty|greater_than_equal_to[1]|less_than_equal_to[5]',
    ];
}
