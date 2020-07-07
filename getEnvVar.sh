#!/bin/sh

while IFS= read -r line; do
    
    IFS='=' read p1 p2 <<< $line
    
    if [ -z "$p2" ] 
    then
        continue
    fi

    if [ "$2" == "$p1" ]
    then
        echo $p2
    fi

done < "$1"