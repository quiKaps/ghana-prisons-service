<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;

class ViewInmate extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.station.pages.view-inmate';
}


// CREATE USER 'gps'@'%' IDENTIFIED BY 'password'; GRANT ALL PRIVILEGES ON *.* TO 'gps'@'%' WITH GRANT OPTION; FLUSH PRIVILEGES; EXIT;

// <VirtualHost *:80>
//       ServerName http://18.198.24.16
//       DocumentRoot /var/www/html/gps/public

//       <Directory /var/www/html/gps/public>
//          AllowOverride All
//          Require all granted
//      </Directory>

//      ErrorLog ${APACHE_LOG_DIR}/error.log
//      CustomLog ${APACHE_LOG_DIR}/access.log combined
//  </VirtualHost>

// ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIEdB5U/nv2rNfgY1Fn6zRT/5X9JBRCPHlB1pn1Oresqr oheneadjei.dev@gmail.com
