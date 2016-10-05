= How to install

Ubuntu @fixme

== Syntax checking for .sql files (PostgreSQL)

```bash
sudo apt-get install python-pip
sudo apt-get install libecpg-dev
sudo pip install pgsanity
```

== Syntax checking for .po files

```bash
sudo aptitude install gettext
```

=== @fixme
```
msgfmt => plural handling is a GNU gettext extension
```

```xml
<target name="lint" depends="php-lint,sql-lint,po-lint,js-lint,css-lint"/>
```

== Syntax checking for .js files

```bash
sudo aptitude install nodejs nodejs-legacy npm
sudo npm install -g esprima
```

== Syntax checking for .css files

```bash
sudo aptitude install nodejs nodejs-legacy npm
sudo npm install -g csslint
```

== @fixme

all@16.04, including xmllint, etc...

== Documentation

```bash
pear channel-discover pear.phpdoc.org
pear install phpdoc/phpDocumentor-alpha
extension=xmlreader.so
extension=xsl.so
```

== Checkstyle

```bash
mv vendor/wimg/php-compatibility vendor/wimg/PHPCompatibility
vendor/bin/phpcs - -config-set installed_paths vendor/cakephp/cakephp-codesniffer,vendor/wimg
```