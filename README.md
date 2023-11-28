# Requerimientos 
Composer
XAMPP o el de su preferencia
Php 8 con oci8 para conexión a BD de Oracle (Puede seguir el siguiente tutorial: https://blogs.oracle.com/opal/post/installing-xampp-on-windows-for-php-and-oracle-database)

habilitar en el archivo php.ini la extensión: extension=ldap 

# Pasos a seguir
TODO: Guide users through getting your code up and running on their own system. In this section you can talk about:

1.  Clonar el repositorio

git clone 

2.  Acceder al directorio del repositorio desde la consola de comandos

cd personeria-api

3.  Instalar dependencias

Ejecute el comando a continuación, el cual le permitirá obtener las librerías necesarias para el correcto funcionamiento: 

composer install

4.  Copiar contenido del archivo .env.example al .env

cp .env.example .env
