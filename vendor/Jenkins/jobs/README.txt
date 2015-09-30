sudo ant -u jenkins ant phpunit -f plugins/Database/vendor/Jenkins/build.xml

wget http://localhost:8080/jnlpJars/jenkins-cli.jar
/usr/lib/java/bin/java -jar jenkins-cli.jar -s http://localhost:8080/ create-job "CakePHP 3 Plugin Database" < "plugins/Database/vendor/Jenkins/jobs/CakePHP3-Database-Plugin.xml"
/usr/lib/java/bin/java -jar jenkins-cli.jar -s http://localhost:8080/ create-job "CakePHP 3 Plugin Database Quality" < "plugins/Database/vendor/Jenkins/jobs/CakePHP3-Database-Plugin-Quality.xml"