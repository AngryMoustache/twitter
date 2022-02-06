<?php

namespace AngryMoustache\Twitter;

use Illuminate\Support\Str;

class Tweet
{
    public $id;
    public $text;
    public $author;
    public $attachments;
    public $video;
    public $source;

    public function __construct(array $tweet = [])
    {
        $this->id = $tweet['id'] ?? null;
        $this->text = $tweet['text'] ?? null;
        $this->author = $tweet['author'] ?? null;
        $this->attachments = $tweet['attachments'] ?? null;
        $this->video = $tweet['video'] ?? null;
        $this->source = $tweet['entities']['urls'][0]['url'] ?? null;
    }

    public function getMainImage()
    {
        return $this->attachments[0]
            ?? $this->getVideo()['preview_image']
            ?? null;
    }

    public function getVideo()
    {
        $videoId = Str::of($this->video[0] ?? '')
            ->afterLast('/tweet_video_thumb/')
            ->beforeLast('.')
            ->__toString();

        if (! $videoId) {
            return null;
        }

        return [
            'preview_image' => $this->video[0],
            'video_url' => "https://video.twimg.com/tweet_video/${videoId}.mp4",
        ];
    }
}
