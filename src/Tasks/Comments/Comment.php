<?php

namespace ArtemMatveev\TestTask\Tasks\Comments;

class Comment
{
    /* Comment id */
    protected ?int $id;
    /* Comment name*/
    protected ?string $name;
    /* Comment text*/
    protected ?string $text;

    /**
     * @param array|null $properties object initialization data
     */
    public function __construct($properties = [])
    {
        $this->id = $properties['id'] ?? null;
        $this->name = $properties['name'] ?? null;
        $this->text = $properties['text'] ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id)
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text)
    {
        $this->text = $text;
    }

    /**
     * Converts Comment object to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'name' => $this->getName(),
            'text' => $this->getText(),
        ];

        if ($this->getId() !== null) {
            $result['id'] = $this->getId();
        }

        return $result;
    }
}