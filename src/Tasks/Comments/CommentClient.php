<?php

namespace ArtemMatveev\TestTask\Tasks\Comments;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\InvalidArgumentException;
use ArtemMatveev\TestTask\Tasks\Comments\Comment;

class CommentClient
{
    /** GET HTTP request */
    const TYPE_GET = 'GET';
    /** POST HTTP request */
    const TYPE_POST = 'POST';
    /** PUT HTTP request */
    const TYPE_PUT = 'PUT';
    /** DELETE HTTP request */
    const TYPE_DEL = 'DELETE';

    protected string $targetUrl;
    protected int $statusCode;

    protected Client $client;

    /**
     * @param string|null $targetUrl API Server URL
     */
    public function __construct(string $targetUrl = 'http://localhost:80/v1/comment')
    {
        $this->targetUrl = $targetUrl;
        $this->statusCode = 0;
        $this->client = new Client();
    }

    /**
     * Sends HTTP request
     * 
     * @param string $url URL to send
     * @param string $type HTTP request type
     * @param array|null $data request data
     * @return string response
     * @throws ConnectException if connection cannot be established
     * @throws ServerException if present 5xx error
     * @throws ClientException if present 4xx error
     */
    protected function send(string $url, string $type, ?array $data = null): string
    {
        $data = $data ?: [];

        try {
            if (($response = $this->client->request($type, $url, $data))->getStatusCode() >= 300) {
                throw new \Exception($response->getBody()->getContents(), $response->getStatusCode());
            }
        } catch (ConnectException $e) {
            $this->statusCode = 0;
            throw new \Exception($e->getMessage());
        } catch (ClientException | ServerException $e) {
            $this->statusCode = $e->getResponse()->getStatusCode();
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }

        $this->statusCode = $response->getStatusCode();
        return $response->getBody()->getContents();
    }

    /**
     * Returns last HTTP status
     * 
     * @return int
     */
    public function lastStatusCode(): int
    {
        return $this->statusCode;
    }


    /**
     * Gets all comments
     * 
     * @return Comment[]
     */
    public function getAll(): array
    {
        $result = [];

        $items = $this->send($this->targetUrl . 's', static::TYPE_GET);
        foreach (json_decode($items) as $item) {
            $result[] = new Comment(json_decode(json_encode($item),true));
        }

        return $result;
    }

    /**
     * Gets one comment
     * 
     * @param int $id comment ID
     * @return Comment
     */
    public function getOne(int $id): Comment
    {
        $result = $this->send($this->targetUrl . '/' . $id, static::TYPE_GET);

        return new Comment(json_decode($result, true));
    }

    /**
     * Updates current comment
     * 
     * @param Comment $comment
     * @return Comment
     */
    public function updateOne(Comment $comment): Comment
    {
        $data = $comment->toArray();

        if (key_exists('id', $data)) {
            unset($data['id']);
        }

        $result = $this->send($this->targetUrl . '/' . $comment->getId(), static::TYPE_PUT, ['form_params' => $data]);

        return new Comment(json_decode($result, true));
    }

    /**
     * Adds new comment
     * 
     * @param Comment $comment
     * @return Comment
     */
    public function addOne(Comment $comment): Comment
    {
        if (!$comment->getText() || $comment->getText() === '') {
            throw new InvalidArgumentException('Comment::$text can not be empty');
        }

        if (!$comment->getName() || $comment->getName() === '') {
            throw new InvalidArgumentException('Comment::$name can not be empty');
        }

        $data = $comment->toArray();

        if (key_exists('id', $data)) {
            unset($data['id']);
        }

        $result = $this->send($this->targetUrl, static::TYPE_POST, ['form_params' => $data]);

        return new Comment(json_decode($result, true));
    }

    /**
     * Deletes comment
     * 
     * @param Comment $comment existing comment
     * @return int operation status code
     */
    public function deleteOne(Comment $comment): int
    {
        $this->send($this->targetUrl . '/' . $comment->getId(), static::TYPE_DEL);

        return $this->lastStatusCode();
    }
}
