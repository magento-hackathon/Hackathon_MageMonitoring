#!/bin/zsh

# Very simple and stupid modman generator...
rm modman
for f in  $(find ${PWD}/../src/* -type d -not -path '*\/.*')
    do
    if [ $f != 'loop.sh' ]
        then
           echo $f   $f >> modman
    fi
    done
