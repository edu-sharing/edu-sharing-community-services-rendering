Run `docker compose up -d`

The current php directory will be mounted into an php docker container including the necessary deps.

Open the [install page](http://localhost:8081)

Enter as db:
* host: `db`
* database: `rendering`
* username: `rendering`
* password: `rendering`
* Repository URL: When hosted locally, make sure to use the external ip of your current system, localhost won't work!

Note: You might need to chown/chmod on your current directory so that the rendering service can write configs.

After install succeeded, register it in the admin tools of your repository via url: `http://localhost:8081/application/esmain/metadata.php`

If loading of repo data fails, go into `conf/esmain/app-local.properties.xml` and replace the local adress with your external ip in the key `authenticationwebservice`

Note: You might have to run `composer install --ignore-platform-reqs` in your project folder.