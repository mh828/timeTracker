<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/4/2019
 * Time: 00:22
 */

global $TITLE;
$TITLE = 'تست برنامه';

Statics::addBundle(Statics::BUNDLE_JDF);

function body(){
    var_dump(getdate());
}