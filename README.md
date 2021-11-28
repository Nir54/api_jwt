# api_jwt

composer install

mkdir -p config\jwt

Copy JWT_PASSPHRASE in env after execute 
openssl genrsa -out config/jwt/private.pem -aes256 4096

Copy JWT_PASSPHRASE in env after execute 
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

php bin/console d:d:c

php bin/console doctrine:schema:update -f
 