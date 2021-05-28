<?php

namespace AngryMoustache\Twitter;

class Tweet
{
    public $id;
    public $text;
    public $author;
    public $attachments;
    public $source;

    public function __construct(array $tweet = [])
    {
        $this->id = $tweet['id'] ?? null;
        $this->text = $tweet['text'] ?? null;
        $this->author = $tweet['author'] ?? null;
        $this->attachments = $tweet['attachments'] ?? null;
        $this->source = $tweet['entities']['urls'][0]['url'] ?? null;
    }

    public function getMainImage()
    {
        return $this->attachments[0] ?? null;
    }
}
