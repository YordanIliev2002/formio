```shell
docker rm formio-mysql
docker run --name formio-mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=formio-password -d mysql:8
docker exec -it formio-mysql bash
mysql -p
<enter the password>
```
