#!/bin/sh

echo "\t############# SETUP ENV VAR #############\n"

while IFS= read -r line; do
    
    IFS='=' read p1 p2 <<< $line
    
    if [ -z "$p2" ] 
    then
        continue
    fi

    if [ "$2" == "$p1" ]
    then
        echo "ENV VALUE : ${p2}"
        export $2=$p2
    fi

done < "$1"