<?php

namespace DOLucas\Concurso\Service;

use DOLucas\Concurso\Service\ConcursoService;
use DOLucas\Concurso\Mapper\ReceiverMapper;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

class ReceiverService
{
    protected $concursoService;
    protected $receiverMapper;
    protected $urls;

    public function __construct(
        ConcursoService $concursoService,
        ReceiverMapper $receiverMapper
    ) {
        $this->concursoService = $concursoService;
        $this->receiverMapper = $receiverMapper;
    }

    public function notify()
    {
        $intituicoes = $this->concursoService->getInstituicoes();
        $receivers = $this->receiverMapper->findAll();

        $emailsNotified = [];
        foreach ($receivers as $receiver) {
            $concursosToSend = [];
            foreach ($receiver['instituicoes'] as $instituicaoReceiver) {
                if ($this->almostSureInArray($instituicaoReceiver, $intituicoes)) {
                    $concursosToSend[] = [
                        'instituicao' => $instituicaoReceiver,
                        'link' => $this->concursoService->getVagaLink($instituicaoReceiver)
                    ];
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
     * Para não precisar digitar o nome da institução
     * exatamente igual está no site do gov :)
     * @param string $needle
     * @param array $instituicoes
     * @return boolean
     */
    protected function almostSureInArray($needle, array $instituicoes)
    {
        foreach ($instituicoes as $instituicao) {
            similar_text($needle, $instituicao, $percent);
            if ($percent >= 80) {
                return true;
            }
        }
        return false;
    }

    protected function send(array $concursos, array $receiver)
    {
        $arrMessage = [];
        foreach ($concursos as $concurso) {
            $arrMessage[] = sprintf(
                '<a href="%s">%s</a>',
                $concurso['link'],
                $concurso['instituicao']
            );
        }

        $textMessages = implode('<br>', $arrMessage);

        $message = new Message();
        $message
            ->setFrom($receiver['from'])
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
