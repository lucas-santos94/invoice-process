# Invoice Process

Este projeto é uma API Laravel para processamento de boletos. Ele foi desenvolvido para processar arquivos CSV, gerar boletos e enviar e-mails com os boletos gerados. A API foi desenvolvida para rodar dentro de contêineres Docker, o que facilita a configuração e execução em qualquer ambiente.

## Pré-requisitos

Antes de rodar o projeto, você precisa ter as seguintes ferramentas instaladas:

- **Docker**: Para rodar o projeto em contêineres.
- **Docker Compose**: Para orquestrar os contêineres.
- **PHP**: Para executar os testes e o desenvolvimento localmente.
- **Composer**: Para gerenciar as dependências do PHP.

## Como rodar o projeto com Docker

### Passos:

1. Clone o repositório para o seu computador:

    ```bash
    git clone https://github.com/lucas-santos94/invoice-process.git
    cd invoice-process
    ```

2. Certifique-se de ter o **Docker** e o **Docker Compose** instalados em sua máquina.

3. No diretório raiz do projeto, crie o arquivo `.env` baseado no exemplo abaixo:

    ```bash
    cp .env.example .env
    ```

4. Verifique o arquivo `.env` para garantir que as configurações de banco de dados e outros serviços estão corretas para o seu ambiente.

5. Inicie os contêineres com o comando:

    ```bash
    docker-compose up --build
    ```

6. O Docker irá baixar as imagens necessárias e iniciar os contêineres. O servidor Laravel estará disponível em `http://localhost:8080`.

7. Para rodar as migrações e popular o banco de dados com os dados iniciais, execute o comando:

    ```bash
    docker-compose exec app php artisan migrate
    ```

8. Agora, você pode acessar a API no endereço `http://localhost:8080`.

## Exemplo de arquivo .env

O arquivo `.env` contém variáveis de configuração para a aplicação. Aqui está um exemplo com as configurações principais para o seu projeto:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY={api key}
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=invoice_process
DB_USERNAME=invoice_process
DB_PASSWORD=invoice_process

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

SESSION_DRIVER=file

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0
REDIS_CACHE_DB=1
FILESYSTEM_DISK=local
```

## Exemplo de chamada para a API

Para processar uma fatura, você pode usar o seguinte comando `curl` para enviar um arquivo CSV para a API. A rota da API espera um arquivo CSV que será processado e, em seguida, os boletos serão gerados e enviados por e-mail.

### Exemplo de `curl`:

```bash
curl --location 'http://127.0.0.1:8080/api/invoice/process' \
--header 'Content-Type: application/json' \
--form 'file=@"/path/file/invoices.csv"'