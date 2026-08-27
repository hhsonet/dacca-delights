<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'code', 'category_id', 'slug', 'name', 'note', 'price', 'kcal', 'ingredients',
        'image', 'is_new', 'is_featured', 'min_qty', 'in_bagel_pool', 'is_active',
    ];

    /** Ambiguous characters (0/O/1/I/L) omitted so codes survive being read aloud. */
    private const CODE_LETTERS = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    private const CODE_DIGITS  = '23456789';

    /** A free 6-character code containing at least one letter and one digit. */
    public function generateCode(): string
    {
        do {
            $chars = [
                self::CODE_LETTERS[random_int(0, strlen(self::CODE_LETTERS) - 1)],
                self::CODE_DIGITS[random_int(0, strlen(self::CODE_DIGITS) - 1)],
            ];
            $all = self::CODE_LETTERS . self::CODE_DIGITS;
            for ($i = 0; $i < 4; $i++) {
                $chars[] = $all[random_int(0, strlen($all) - 1)];
            }
            shuffle($chars);
            $code = implode('', $chars);
        } while ($this->where('code', $code)->countAllResults() > 0);

        return $code;
    }

    protected $validationRules = [
        // Required so is_unique's {id} placeholder resolves on update.
        'id'          => 'permit_empty|is_natural_no_zero',
        'name'        => 'required|min_length[2]|max_length[160]',
        'slug'        => 'required|max_length[160]|is_unique[products.slug,id,{id}]',
        'category_id' => 'required|is_natural_no_zero',
        'price'       => 'required|is_natural',
        'kcal'        => 'permit_empty|is_natural',
        'min_qty'     => 'permit_empty|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'slug' => ['is_unique' => 'Another product already uses that slug.'],
        'price' => ['is_natural' => 'Price must be a whole number of taka.'],
    ];

    /** Products joined to their category name, for listings. */
    public function withCategory(): \CodeIgniter\Database\BaseBuilder
    {
        return $this->db->table($this->table . ' p')
            ->select('p.*, c.name AS category_name')
            ->join('categories c', 'c.id = p.category_id', 'left');
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}
