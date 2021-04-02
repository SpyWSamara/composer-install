<?php

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
$path->create();

$composer = new File(
    $path->getPath().'/composer.json'
);
if (!$composer->isExists()) {
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
}


$composerBin = new File(
    $path->getPath().'/composer'
);
if (!$composerBin->isExists()) {
    $composerBinAlias = \sprintf(
        '%s -c %s -f %s $*',
        $php,
        \php_ini_loaded_file(),
        $composerPath
    );
    if (false !== $composerBin->putContents($composerBinAlias)) {
        // 0111 - executable for all
        \chmod($composerBin->getPath(), $composerBin->getPermissions() | 0111);
    }
}
