#!/bin/bash

prepare_dir ()
{
echo " preparing $1 , creating template folders and a symbolic link to core/tpl"

  mkdir ./sites/$1/tpl/cache
  chmod a+w ./sites/$1/tpl/cache
  mkdir ./sites/$1/tpl/templates_c
  chmod a+w ./sites/$1/tpl/templates_c
  ln -s   ../../../core/tpl ./sites/$1/tpl/common

  ln -s   ../../../core/doc_root ./sites/$1/doc_root/common
}



prepare_dir cms
prepare_dir gt
prepare_dir dc
prepare_dir forms

