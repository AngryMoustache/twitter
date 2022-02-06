<?php

namespace AngryMoustache\Twitter;

use Illuminate\Support\Str;

class Tweet
{
    public $id;
    public $text;
    public $author;
    public $media;
    public $source;

    public function __construct(array $tweet = [])
    {
        $this->id = $tweet['id'] ?? null;
        $this->text = $tweet['text'] ?? null;
        $this->author = $tweet['author'] ?? null;
        $this->media = $tweet['media'] ?? null;
        $this->source = $tweet['entities']['urls'][0]['url'] ?? null;
    }

    public function images()
    {
        return collect($this->media)
            ->filter(fn ($item) => ($item['type'] ?? '') === 'photo')
            ->toArray();
    }

    public function image()
    {
        return $this->images()[0] ?? null;
    }

    public function video()
    {
        return collect($this->media)
            ->filter(fn ($item) => ($item['type'] ?? '') !== 'photo')
            ->map(function ($video) {
                $id = Str::of($video['preview_image_url'])->afterLast('/')->beforeLast('.');
                $video['video_url'] = "https://video.twimg.com/tweet_video/${id}.mp4";
                return $video;
            })
            ->first();
    }
}
