#!/bin/sh

[ ! -d "$HOME/bin" ] && mkdir -p $HOME/bin
# check home bin folder in PATH
if [[ ! $PATH =~ $HOME/bin ]]; then
    echo 'Add to your rc-file `export PATH=$HOME/bin:$PATH`'
fi

# https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md
EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    echo >&2 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --install-dir="$HOME/bin" --filename=composer
RESULT=$?
rm composer-setup.php
exit $RESULT
