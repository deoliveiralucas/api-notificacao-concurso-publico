<?php

namespace DOLucas\Concurso\Mapper;

use RuntimeException;

class ReceiverMapper
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @param $path string
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        if (! file_exists($this->path)) {
            throw new RuntimeException('arquivo data.json não encontrado na pasta storage');
        }

        return json_decode(file_get_contents($this->path), true);
    }
}
