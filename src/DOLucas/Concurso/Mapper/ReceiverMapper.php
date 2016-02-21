<?php

namespace DOLucas\Concurso\Mapper;

class ReceiverMapper
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function findAll()
    {
        return json_decode(file_get_contents($this->path), true);
    }
}
