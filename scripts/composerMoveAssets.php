<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Filesystem\Filesystem;

$rootDir = dirname(__DIR__);

$jQueryInVendorDir = $rootDir . "/vendor/components/jquery/";
$jQueryInWebDir = $rootDir . "/public/libs/jquery/";
$fileSystem = new Filesystem();
$fileName = 'jquery.min.js';
$fileSystem->copy($jQueryInVendorDir . $fileName, $jQueryInWebDir . $fileName);
printf('Composer: jQuery was copied to %s,%s', $jQueryInWebDir, PHP_EOL);

$bootstrapInVendorDir = $rootDir . "/vendor/twbs/bootstrap/";
$bootstrapInVendorDistDir = $bootstrapInVendorDir . "dist/";
$bootstrapInWebDir = $rootDir . "/public/libs/bootstrap/";
$fileName = 'css/bootstrap.min.css';
$fileSystem->copy($bootstrapInVendorDistDir . $fileName, $bootstrapInWebDir . $fileName);
$fileName = 'js/bootstrap.min.js';
$fileSystem->copy($bootstrapInVendorDistDir . $fileName, $bootstrapInWebDir . $fileName);
$fileName = 'js/tab.js';
$fileSystem->copy($bootstrapInVendorDir . $fileName, $bootstrapInWebDir . $fileName);
printf('Composer: Bootstrap was copied to %s,%s', $bootstrapInWebDir, PHP_EOL);

$select2InVendorDir = $rootDir . "/vendor/select2/select2/dist/";
$select2InWebDir = $rootDir . "/public/libs/select2/";
$fileSystem = new Filesystem();
$fileName = 'css/select2.min.css';
$fileSystem->copy($select2InVendorDir . $fileName, $select2InWebDir . $fileName);
$fileName = 'js/select2.js';
$fileSystem->copy($select2InVendorDir . $fileName, $select2InWebDir . $fileName);
$fileSystem->mirror($select2InVendorDir . 'js/i18n', $select2InWebDir . 'js/i18n');
printf('Composer: Select2 was copied to %s,%s', $select2InWebDir, PHP_EOL);
