#!/bin/bash

full_path=$(realpath $0)
currentdir=$(dirname $full_path)
php $currentdir/cleanup.php
