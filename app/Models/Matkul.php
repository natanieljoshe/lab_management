<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matkul extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'sks',
        'curriculum_semester',
        'is_required',
        'root_folder_id',
        'open_file_access',
    ];

    protected $casts = [
        'open_file_access' => 'boolean',
        'is_required' => 'boolean',
    ];

    public function rootFolder()
    {
        return $this->belongsTo(Folders::class, 'root_folder_id');
    }

    public function files()
    {
        return $this->belongsToMany(Files::class, 'matkul_files')->withTimestamps();
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'matkul_id');
    }

    public function matkulConfigs()
    {
        return $this->hasMany(MatkulConfig::class, 'matkul_id');
    }

    public function classSections()
    {
        return $this->hasMany(ClassSection::class, 'matkul_id');
    }
}
