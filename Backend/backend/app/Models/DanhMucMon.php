<?php
/**
 * ============================================================================
 * MODEL DANHMUCMON - DANH MỤC MÓN ĂN
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucMon extends Model
{
    use HasFactory;

    protected $table = 'danhmucmon';
    protected $primaryKey = 'DanhMucID';
    public $timestamps = false;

    protected $fillable = [
        'TenDanhMuc'
    ];

    /**
     * Quan hệ với MonAn
     */
    public function monAn()
    {
        return $this->hasMany(MonAn::class, 'DanhMucID', 'DanhMucID');
    }
}
