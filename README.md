## API JSON e notificação por e-mail de concursos públicos

Aplicação para enviar email quando encontrar inscrições de concursos públicos abertas, com API JSON.

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

- **Próximos concursos**

    `[GET] /api/concursos/proximo`

- **Concursos abertos**

    `[GET] /api/concursos/aberto`

- **Concursos em andamento**

    `[GET] /api/concursos/andamento`

- **Concursos encerrado**

    `[GET] /api/concursos/encerrado`

## Contribua ##

- Veja [CONTRIBUTING](CONTRIBUTING.md) para mais detalhes.