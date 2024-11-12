<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostHistory extends Model
{
    use HasFactory;

    protected $table   = 'posts_histories';
    protected $guarded = 'id';

    protected $fillable = [
        'post_id',
        'judul',
        'nama_pembuat',
        'isi_posts',
    ];
}
