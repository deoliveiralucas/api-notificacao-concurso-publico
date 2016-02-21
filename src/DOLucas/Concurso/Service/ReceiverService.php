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
    )
    {
        $this->concursoService = $concursoService;
        $this->receiverMapper = $receiverMapper;
    }

    public function notify()
    {
        $intituicoes = $this->concursoService->getInstituicoes();
        $receivers = $this->receiverMapper->findAll();

        foreach ($receivers as $receiver) {
            $concursosToSend = [];
            foreach ($receiver['instituicoes'] as $instituicaoReceiver) {
                if (in_array($instituicaoReceiver, $intituicoes)) {
                    $concursosToSend[] = [
                        'instituicao' => $instituicaoReceiver,
                        'link' => $this->concursoService->getVagaLink($instituicaoReceiver)
                    ];
                }
            }

            $this->send($concursosToSend, $receiver['email']);
        }
    }

    protected function send(array $concursos, $email)
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
            ->setFrom(sprintf('Aviso de Concurso <%s>', $email))
            ->addTo($email)
            ->setSubject('Aviso de Concurso')
            ->setHTMLBody(sprintf(
                'Existem concursos abertos para as instituições de sua preferência.<br><br>%s',
                $textMessages
            ));

        $mailer = new SendmailMailer();
        $mailer->send($message);
    }
}
