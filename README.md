## How to run the project for development purposes
- start the DB and Apache from XAMPP
- click on the "admin" panel next to the DB in XAMPP. This should open myPhpAdmin
- open the SQL tab and paste the content of `db.sql`. This will create the DB and tables.
- launch the application
- create a folder `uploads` at the root level of the repo
```shell
cd php
C:\xampp\php\php.exe -S localhost:8000
```

## How to run it using XAMPP for demo
- configure the credentials for the db in `db_connection.php`
- create the folder `C:/xampp/htdocs/whatever/uploads`
- copy the contents of the PHP folder into `C:/xampp/htdocs/whatever`
- run XAMPP - Apache and MySQL
- execute `db.sql` inside the db
