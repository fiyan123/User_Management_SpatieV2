<?php

namespace App\Observers;

// use App\Models\Post;

use App\Models\PostHistory;
use App\Models\Posts;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Posts $post): void
    {
        //
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updating(Posts $post): void
    {
        PostHistory::create([
            'post_id' => $post->id,
            'judul' => $post->getOriginal('judul'),
            'nama_pembuat' => $post->getOriginal('nama_pembuat'),
            'isi_posts' => $post->getOriginal('isi_posts'),
        ]);
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Posts $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Posts $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Posts $post): void
    {
        //
    }
}
