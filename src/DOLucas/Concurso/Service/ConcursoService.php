<?php

namespace DOLucas\Concurso\Service;

use DOLucas\Concurso\Mapper\ConcursoMapper;
use DOMDocument;
use InvalidArgumentException;

class ConcursoService
{

    /**
     * @var string
     */
    const STATUS_PROXIMO = 'proximo';

    /**
     * @var string
     */
    const STATUS_ABERTO = 'aberto';

    /**
     * @var string
     */
    const STATUS_ANDAMENTO = 'andamento';

    /**
     * @var string
     */
    const STATUS_ENCERRADO = 'encerrado';

    /**
     * @var string
     */
    const URL_DETALHE = 'url_detalhe';

    /**
     * @var array
     */
    protected $urls;

    /**
     * @var array
     */
    protected $concursos = [];

    /**
     * @var string
     */
    protected $status;

    /**
     * @param array $urls
     * @param string $status
     */
    public function __construct(array $urls)
    {
        $this->urls = $urls;
    }

    /**
     * @param string $status
     * @throws InvalidArgumentException
     */
    public function setStatus($status)
    {
        $valid = array(
            static::STATUS_PROXIMO,
            static::STATUS_ABERTO,
            static::STATUS_ANDAMENTO,
            static::STATUS_ENCERRADO
        );

        if (! in_array($status, $valid)) {
            throw new InvalidArgumentException(sprintf('status %s inválido', $status));
        }

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return ConcursoService
     */
    public function loadConcursos()
    {
        $domConcursosGov = file_get_contents($this->urls[$this->getStatus()]);

        $dOM = new DOMDocument();
        @$dOM->loadHTML($domConcursosGov);

        $rows = $dOM->getElementsByTagName('tr');

        $concursos = [];
        foreach ($rows as $row) {
            $idConcurso = $row->getAttribute('id');
            if (is_numeric($idConcurso)) {
                $concursos[] = $this->getColsAsArray($row->childNodes, $idConcurso);
            }
        }

        $this->concursos = $concursos;
        return $this;
    }

    /**
     * @param string $status
     * @return array
     */
    public function getConcursos($status = null)
    {
        if (is_null($status)) {
            $status = static::STATUS_ABERTO;
        }

        $this->setStatus($status);

        if (! count($this->concursos) || $status !== $this->getStatus()) {
            $this->loadConcursos();
        }

        return $this->concursos;
    }

    /**
     * @param object $columns
     * @param int $intituicaoId
     * @return array
     */
    protected function getColsAsArray($columns, $intituicaoId)
    {
        $infoVaga = [];
        foreach ($columns as $col) {
            if (($dado = trim($col->nodeValue)) !== '') {
                $infoVaga[] = $dado;
            }
        }

        if (count($infoVaga)) {
            $linkDetalhes = sprintf(
                $this->urls[static::URL_DETALHE],
                $intituicaoId
            );

            return array(
                'instituicao'  => isset($infoVaga[0]) ? $infoVaga[0] : null,
                'cargo'        => isset($infoVaga[1]) ? $infoVaga[1] : null,
                'escolaridade' => isset($infoVaga[2]) ? $infoVaga[2] : null,
                'salario'      => isset($infoVaga[4]) ? $infoVaga[4] : null,
                'inscricoes'   => isset($infoVaga[6]) ? $infoVaga[6] : null,
                'detalhes'     => $linkDetalhes
            );
        }

        return [];
    }
}
