# api_jwt

composer install

mkdir config\jwt

Copy JWT_PASSPHRASE in env after execute 
openssl genrsa -out config/jwt/private.pem -aes256 4096

