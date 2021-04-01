# Install composer to hosting and setup in Bitrix actually

## Install composer

### Safe method

Clone this repo:

``` sh
$ git clone https://github.com/SpyWSamara/composer-install
```

Enter repo directory and run `install.sh` script:

``` sh
$ ./install.sh
```

Remove not nessasory repo:

``` sh
$ rm composer-install
```

### Semi-safe method

Clone and install with one-line command:

``` sh
$ git clone https://github.com/SpyWSamara/composer-install && ./composer-install/install.sh && rm -rf composer-install
```

### Brave spirit method

Execute install script right from `curl` :

``` sh
$ sh -c "$(curl -fsSL https://raw.githubusercontent.com/SpyWSamara/composer-install/main/install.sh)"
```

Execute install script right from `wget` :

``` sh
$ sh -c "$(wget https://raw.githubusercontent.com/SpyWSamara/composer-install/main/install.sh -O -)"
```

## Bitrix setup script

Past the path to php cli and composer binary. Execute script in Bitrix php-console:

``` php
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;

$php = '[path-to-php-cli]';
$composerPath = '[path-to-composer.phar]';
if (empty($php)) {
    throw new \Exception('Path to php cli cant be empty!');
}
if (!\file_exists($php)) {
    throw new \Exception('Path to php cli not exists!');
}
if (empty($composerPath)) {
    throw new \Exception('Path to composer cant be empty!');
}
if (!\file_exists($composerPath)) {
    throw new \Exception('Path to composer not exists!');
}

$path = new Directory(
    Application::getDocumentRoot().'/local'
);

if (!$path->isExists()) {
    $path->create();
}

$composer = new File(
    $path->getPath().'/composer.json'
);
$composerConfig = [
    'config' => [
        'sort-packages' => true,
        'optimize-autoloader' => true,
    ],
    'autoload' => [
        'psr-4' => [
            'Local\\' => 'src/',
        ],
    ],
    'require' => [
        'wikimedia/composer-merge-plugin' => 'dev-master',
    ],
    'extra' => [
        'merge-plugin' => [
            'require' => [
                '../bitrix/composer-bx.json',
            ],
        ],
    ],
];
$composerJson = \json_encode(
    $composerConfig,
    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
);
$composer->putContents($composerJson);

$composerBin = new File(
    $path->getPath().'/composer'
);
$composerBinAlias = \sprintf(
    '%s -c %s -f %s $*',
    $php,
    \php_ini_loaded_file(),
    $composerPath
);
$composerBin->putContents($composerBinAlias);
// 0111 - executable for all
\chmod($composerBin->getPath(), $composerBin->getPermissions() | 0111);
```

Script source is in `bitrix.php` from root of this repository.

## Use composer

Now you can execute composer from `ssh` . Go to Bitrix local path:

``` sh
$ cd path/to/bitrix/documentroot/local
```

Use `composer` :

``` sh
$ ./composer --help
$ ./composer diagnose
$ ./composer req symfony/var-dumper:^4.2 --dev
$ ./composer update --dry-run
```
