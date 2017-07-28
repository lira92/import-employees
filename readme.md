# Import Employees Aplication

Considering that yout have php5.6+ and composer installed, after cloning 
this aplication execute:

`composer install`

This command will install all dependencies for application and create the 
*storage/app/files_to_be_imported* and *storage/app/imported_files* folders, 
that be used to put files to be imported and files already imported 
respectively. After the packages are installed, configure your database location 
in .env file, changing values if necessary for this keys:

`DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
`

After your database location is correctly configured, execute the migrations for 
create the database schema:

`php artisan migrate`

For populate the database, execute:

`php artisan db:seed`

Copy your csv files that you want import to 
*storage/app/files_to_be_imported* and execute:

`php artisan employees:import`

After the application import your csv files, it copy them to 
*storage/app/imported_files* folder.

The aplication register logs for inconsistences in 
*storage/logs/import_employees.log*

If you want to execute tests, execute:

`./vendor/bin/phpunit`