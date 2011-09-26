#!/bin/bash
# push-to-live.bash - script to push new PA files from beta into production
#file=push-to-live.txt
#cat $file
if [ $# -ne 1 ]; then
    echo
    echo "ERROR: insufficient arguments"
    echo "Usage: $0 <name of file>"
    echo
    echo "e.g. $0 www/index.php"
    echo
    exit 1
fi

file=$1
echo "File = $file"
timestamp=`date +%s`
pushd .
cd $HOME
echo "Current directory: $PWD"
tarfile="$HOME/backup/backup-$timestamp.tar.gz"
echo "*** Backing $file up to $tarfile..."
tar -czvf $tarfile $file
echo "*** Copying Planet-Angel-Website/$file to $file..."
cp Planet-Angel-Website/$file $file
echo "done!"
popd
