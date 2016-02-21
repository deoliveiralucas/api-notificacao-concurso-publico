<?php

namespace DOLucas\Concurso\Service;

use DOLucas\Concurso\Mapper\ConcursoMapper;
use \DOMDocument;

class ConcursoService
{
    protected $urls;
    protected $concursos = [];

    public function __construct(array $urls, $loadConcursos = false)
    {
        $this->urls = $urls;

        if ($loadConcursos === true) {
            $this->loadConcursos();
        }
    }

    public function loadConcursos()
    {
        $domConcursosGov = file_get_contents($this->urls['concursos_abertos']);

        $DOM = new DOMDocument();
        @$DOM->loadHTML($domConcursosGov); // :)

        $rows = $DOM->getElementsByTagName('tr');

        $concursos = [];
        foreach ($rows as $row) {
            $idConcurso = $row->getAttribute('id');
            if (is_numeric($idConcurso)) {
                $concursos[$idConcurso] = $this->getColsAsArray($row->childNodes);
            }
        }

        $this->concursos = $concursos;
        return $this;
    }

    public function getConcursos()
    {
        if (! count($this->concursos)) {
            $this->loadConcursos();
        }
        return $this->concursos;
    }

    public function getInstituicoes()
    {
        $instituicoes = [];
        foreach ($this->getConcursos() as $concurso) {
            $instituicoes[] = $concurso[0];
        }

        return $instituicoes;
    }

    public function getVagaLink($instituicao)
    {
        $intituicaoId = null;
        foreach ($this->getConcursos() as $id => $infoConcurso) {
            if ($infoConcurso[0] == $instituicao) {
                $intituicaoId = $id;
                break;
            }
        }

        return sprintf(
            $this->urls['detalhes_concurso'],
            $intituicaoId
        );
    }

    protected function getColsAsArray($cols)
    {
        $infoVaga = [];
        foreach ($cols as $col) {
            if (($dado = trim($col->nodeValue)) !== '') {
                $infoVaga[] = $dado;
            }
        }

        return $infoVaga;
    }
}
