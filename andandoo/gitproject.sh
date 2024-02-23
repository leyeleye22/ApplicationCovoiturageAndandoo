#!/bin/bash

if [ -z "$1" ]; then
  echo "Erreur : Veuillez fournir un message de commit."
  echo "Utilisation : $0 <message_de_commit>"
  exit 1
fi

cd "C:\\Users\\simplon\\Downloads\\ApplicationCovoiturageAndandoo\\andandoo"

git add .

git commit -m "$1"
git push origin develop
exit 0
