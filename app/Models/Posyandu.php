<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;

    protected $table = 'posyandu';
    protected $fillable = ['nama_posyandu', 'rw', 'alamat', 'nama_ketua'];

    public function users()      { return $this->hasMany(User::class, 'posyandu_id'); }
    public function balitas()    { return $this->hasMany(Balita::class, 'posyandu_id'); }
    public function ibuHamils()  { return $this->hasMany(IbuHamil::class, 'posyandu_id'); }

    public function jadwals()
    {
        return $this->hasMany(JadwalPosyandu::class, 'posyandu_id');
    }
}