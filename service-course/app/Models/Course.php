<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
    
    protected $fillable = [
        'name',
        'certificate',
        'thumbnail',
        'type',
        'status',
        'price',
        'level',
        'description',
        'mentor_id'
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    public function chapter()
    {
        return $this->hasMany(Chapter::class)->orderBy('id', 'DESC');
    }

    public function images()
    {
        return $this->hasMany(ImageCourse::class)->orderBy('id', 'DESC');
    }

    public function mycourse()
    {
        return $this->hasMany(MyCourse::class)->orderBy('id', 'DESC');
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

}
