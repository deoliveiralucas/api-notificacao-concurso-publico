<?php

namespace DOLucas\Concurso\Service;

use DOLucas\Concurso\Service\ConcursoService;
use DOLucas\Concurso\Mapper\ReceiverMapper;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class ReceiverService
{

    /**
     * @var ConcursoService
     */
    protected $concursoService;

    /**
     * @var ReceiverMapper
     */
    protected $receiverMapper;

    /**
     * @param ConcursoService $concursoService
     * @param ReceiverMapper $receiverMapper
     */
    public function __construct(
        ConcursoService $concursoService,
        ReceiverMapper $receiverMapper
    ) {
        $this->concursoService = $concursoService;
        $this->receiverMapper = $receiverMapper;
    }

    /**
     * @return array
     */
    public function notify()
    {
        $concursos = $this->concursoService->getConcursos();
        $receivers = $this->receiverMapper->findAll();

        $emailsNotified = [];
        foreach ($receivers as $receiver) {
            $concursosToSend = [];
            foreach ($receiver['instituicoes'] as $instituicaoReceiver) {
                if ($concurso = $this->almostSureInArray($instituicaoReceiver, $concursos)) {
                    $concursosToSend[] = $concurso;
                }
            }

            if (count($concursosToSend)) {
                $emailsNotified[] = $receiver['email'];
                $this->send($concursosToSend, $receiver);
            }
        }

        return $emailsNotified;
    }

    /**
     * Para não precisar digitar o nome da instituição
     * exatamente como está no site do gov :)
     *
     * @param string $needle
     * @param array $concursos
     * @return array|bool
     */
    protected function almostSureInArray($needle, array $concursos)
    {
        foreach ($concursos as $concurso) {
            similar_text($needle, $concurso['instituicao'], $percent);
            if ($percent >= 80) {
                return $concurso;
            }
        }
        return false;
    }

    /**
     * @param array $concursos
     * @param array $receiver
     */
    protected function send(array $concursos, array $receiver)
    {
        $arrMessage = [];
        foreach ($concursos as $concurso) {
            $arrMessage[] = sprintf(
                '<a href="%s">%s</a>',
                $concurso['detalhes'],
                $concurso['instituicao']
            );
        }

        $textMessages = implode('<br>', $arrMessage);

        $message = new Message();
        $message
            ->setFrom($receiver['from']['email'], $receiver['from']['name'])
            ->addTo($receiver['email'])
            ->setSubject('Aviso de Concurso')
            ->setHTMLBody(sprintf(
                'Existem concursos abertos para as instituições de sua preferência.<br><br>%s',
                $textMessages
            ));

        $mailer = new SendmailMailer();
        $mailer->send($message);
    }
}
