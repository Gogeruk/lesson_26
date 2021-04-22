#!/bin/bash
printf 'DIE YOU PATHETIC DB!\n'
php artisan db:wipe

printf '\nNOW REFRESHING DB.\n'
php artisan migrate:refresh

printf '\nSEEDING DB WITH DATA\n'
php artisan db:seed

printf '\nDONE! PLEASE CHECK YOUR DB.\n\n'
