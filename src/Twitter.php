<?php

namespace AngryMoustache\Twitter;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Twitter
{
    public $options = [];
    public $baseUrl;
    public $apiKey;
    public $secretKey;
    public $userId;
    public $bearerToken;

    public function __construct()
    {
        $this->baseUrl = 'https://api.twitter.com/2';
        $this->apiKey = setting('twitter_api_key');
        $this->secretKey = setting('twitter_secret_key');
        $this->bearerToken = setting('twitter_bearer_token');
        $this->userId = setting('twitter_user_id');
    }

    public function likes($page = 1)
    {
        $this->options = [
            'media.fields' => 'media_key,duration_ms,width,height,preview_image_url,type,url,alt_text',
            'expansions' => 'attachments.media_keys,author_id',
            'tweet.fields' => 'attachments,author_id,context_annotations,conversation_id,created_at,entities,geo,id,in_reply_to_user_id,lang,public_metrics,possibly_sensitive,referenced_tweets,reply_settings,source,text,withheld',
            'user.fields' => 'username',
        ];

        for ($i = 0; $i < $page; $i++) {
            if ($this->options['pagination_token'] ?? null || $i === 0) {
                $tweets = $this->get('/users/:id/liked_tweets');
                $this->options['pagination_token'] = $tweets['meta']['next_token'] ?? null;
            }
        }

        return $this->convertToTweets($tweets);
    }

    public function get($url)
    {
        $url = str_replace('/:id/', "/{$this->userId}/", $url);

        return Http::withToken($this->bearerToken)
            ->baseUrl($this->baseUrl)
            ->get($url, $this->options)
            ->json();
    }

    private function convertToTweets($tweets)
    {
        $data = $tweets['data'] ?? [];

        $media = collect($tweets['includes']['media'] ?? [])
            ->reject(fn ($item) => $item['type'] === 'video')
            ->mapWithKeys(fn ($item) => [$item['media_key'] => $item])
            ->toArray();

        $authors = collect($tweets['includes']['users'] ?? [])
            ->mapWithKeys(fn ($item) => [$item['id'] => $item['username']])
            ->toArray();

        return collect($data)
            ->map(fn ($tweet) => $this->linkMediaToTweet($tweet, $media))
            ->map(fn ($tweet) => $this->linkAuthorToTweet($tweet, $authors))
            ->mapInto(Tweet::class)
            ->toArray();
    }

    private function linkMediaToTweet($tweet, $media)
    {
        $tweet['media'] = collect($tweet['attachments']['media_keys'] ?? [])
            ->map(fn ($mediaId) => $media[$mediaId] ?? null)
            ->toArray();

        return $tweet;
    }

    private function linkAuthorToTweet($tweet, $authors)
    {
        $tweet['author'] = $authors[$tweet['author_id']] ?? null;
        return $tweet;
    }
}
