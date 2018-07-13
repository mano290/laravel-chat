![Laravel Chat](https://i.imgur.com/EgH4yo0.jpg)

# Laravel Chat com Redis e Socket IO

## Requesitos

 - PHP ^7.1.3
 - Node 6.0+
 - Redis 3+ (Windows -> https://github.com/MicrosoftArchive/redis)
 - MYSQL ou MariaDB

## Instalação

1. Copie o .ENV com o comando 
`` php -r "file_exists('.env') || copy('.env.example', '.env');" ``

2. Instale as bibliotecas via composer
`` composer install ``

3. Gere uma key para sua aplicação
`` php artisan key:generate ``

4. Instale os pacotes do Node
`` npm install ``

5. Instale o laravel echo server 
`` npm install -g laravel-echo-server ``

6. Configure o Laravel Echo Server
`` laravel-echo-server init ``

    - Config Laravel Echo Server
    
      ``` 
      Do you want to run this server in development mode? Yes
      Which port would you like to serve from? 6001
      Which database would you like to use to store presence channel members? redis
      Enter the host of your Laravel authentication server. http://localhost:8000
      Will you be serving on http or https? http
      Do you want to generate a client ID/Key for HTTP API? Yes
      Do you want to setup cross domain access to the API? No
      
      ```
7. Crie um banco de dados chamado `` laravel_chat `` e configure a conexão no `` .env ``

8. E por final rode as migration com `` php artisan migrate ``
  
## Executando a aplicação

1. Abra um shell para deixar o Laravel Echo Server rodando
`` laravel-echo-server start ``

2. Inicie o servidor usando
`` php artisan serve ``

3. Acesse <http://localhost:8000> e pronto! =D