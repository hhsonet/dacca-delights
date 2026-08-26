<?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryZoneModel extends Model
{
    protected $table         = 'delivery_zones';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['name', 'fee', 'is_limited', 'cod_allowed'];

    protected $validationRules = [
        // Required so is_unique's {id} placeholder resolves on update.
        'id'   => 'permit_empty|is_natural_no_zero',
        'name' => 'required|max_length[120]|is_unique[delivery_zones.name,id,{id}]',
        'fee'  => 'permit_empty|is_natural',
    ];

    public function ordered(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }
}
