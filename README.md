## API de concursos públicos com notificação por e-mail

Aplicação para enviar email quando encontrar inscrições de concursos públicos abertas e com uma API JSON.

- *Os dados são buscados no site [concursopublico.sp.gov.br](http://www.concursopublico.sp.gov.br/)*
- *Atualizado: 18/07/2016*

## Instalação

`git clone https://github.com/deoliveiralucas/notificacao-concurso-publico.git`

`cd notificacao-concurso-publico`

`composer install`

`php -S localhost:8888 -t public`

## Configuração

- Renomeie o arquivo `storage/data.json.example` para `storage/data.json` e adicione seus dados.

- Url para enviar notificações `[GET] /notify`

## API concursos

- **Próximos Concursos**

    `[GET] /api/concursos/proximo`

- **Concursos Abertos**

    `[GET] /api/concursos/aberto`

- **Concursos Andamento**

    `[GET] /api/concursos/andamento`

- **Concursos Encerrado**

    `[GET] /api/concursos/encerrado`

## Contribua ##

- Veja [CONTRIBUTING](CONTRIBUTING.md) para mais detalhes.