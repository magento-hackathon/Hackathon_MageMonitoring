#!/bin/zsh

# Very simple and stupid modman generator...
rm modman
for f in  $(find * -type f -not -path '*\/.*')
    do
    if [ $f != 'loop.sh' ]
        then
           echo $f   $f >> modman
    fi
    done
