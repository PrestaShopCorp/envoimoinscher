<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    EnvoiMoinsCher <informationapi@boxtale.com>
* @copyright 2007-2015 PrestaShop SA / 2011-2015 EnvoiMoinsCher
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registred Trademark & Property of PrestaShop SA
*/

require_once(realpath(dirname(__FILE__) . '/../../../config/defines.inc.php'));
require_once(_PS_MODULE_DIR_ . '/../config/config.inc.php');
require_once(_PS_MODULE_DIR_ . '/../init.php');
require_once(_PS_MODULE_DIR_ . '/envoimoinscher/envoimoinscher.php');

$emc = new Envoimoinscher();

$cp = isset($_REQUEST['cp']) && !empty($_REQUEST['cp']) ? $_REQUEST['cp'] :
        EnvoimoinscherModel::getConfig('EMC_POSTALCODE') ;
$ville = isset($_REQUEST['ville']) && !empty($_REQUEST['ville'])? $_REQUEST['ville'] :
        EnvoimoinscherModel::getConfig('EMC_CITY') ;
$country = isset($_REQUEST['country']) ? $_REQUEST['country'] : 'FR' ;
$locale = isset($_REQUEST['locale']) ? $_REQUEST['locale'] : 'fr-FR' ;
$langId = isset($_REQUEST['lang_id']) ? $_REQUEST['lang_id'] : '1' ;
$srv = isset($_REQUEST['srv']) ? $_REQUEST['srv'] : '' ;
$ope = isset($_REQUEST['ope']) ? $_REQUEST['ope'] : '' ;
$inputCallBack = isset($_REQUEST['inputCallBack']) ? $_REQUEST['inputCallBack'] : '' ;
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'html' ;

if (Tools::strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'
    && $type == "json") {// I'm AJAX!
    header("content-type:application/json");
    // on recupere les services depuis le serveur envoimoinscher
    require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/WebService.php');
    require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/ListPoints.php');
    $login = EnvoimoinscherModel::getConfig('EMC_LOGIN');
    $pass = EnvoimoinscherModel::getConfig('EMC_PASS');
    $env = EnvoimoinscherModel::getConfig('EMC_ENV');
    $key = EnvoimoinscherModel::getConfig('EMC_KEY_' . $env);

    $listPoints = new EnvListPoints(array('user' => $login, 'pass' => $pass, 'key' => $key));
    $listPoints->setEnv(Tools::strtolower($env));
    $listPoints->setLocale($locale);
    $listPoints->getListPoints(
        $ope,
        array('srv_code' =>$srv, 'ville' => $ville, 'cp' => $cp, 'pays' => $country, 'collecte' => 'dest')
    );
    $points = $listPoints->list_points;
    die(Tools::jsonEncode($points));
} else {
    $lang = Tools::substr($locale, 0, 2);
    // anglais si traduction pas présente
    if (!in_array($lang, array('fr', 'en', 'es'))) {
        $lang = 'en';
    }

    $aRelayName = array(
        'CHRP' => array(
            'fr' => 'Chrono Relais',
            'en' => 'Chrono Relais',
            'es' => 'Chrono Relais'
        ),
        'MONR' => array(
            'fr' => 'Point Relais',
            'en' => 'Point Relais',
            'es' => 'Punto Pack'
        ),
        'IMXE' => array(
            'fr' => 'Point Relais',
            'en' => 'Point Relais',
            'es' => 'Punto Pack'
        ),
        'SOGP' => array(
            'fr' => 'Relais Colis',
            'en' => 'Relais Colis',
            'es' => 'Relais Colis'
        ),
        'UPSE' => array(
            'fr' => 'relais Access Point',
            'en' => 'Access Point',
            'es' => 'Access Point'
        ),
        'default' => array(
            'fr' => 'Point Relais',
            'en' => 'Relay Point',
            'es' => 'Punto de recogida'
        ),
    );
    $relayName = isset($aRelayName[Tools::strtoupper($ope)])? $aRelayName[$ope][$lang] : $aRelayName['default'][$lang];

    // Script URL
    $host = __PS_BASE_URI__;
    $baseUrl = "/modules/envoimoinscher/ajax/choix-relais.php?";

    // specifique a prestashop
    $requestUri = str_replace(__PS_BASE_URI__, "", $_SERVER['REQUEST_URI']);
    $pos = strpos($requestUri, 'choixRelais&');
    if ($pos) {
        $host = Tools::getShopProtocol() . Tools::getHttpHost().__PS_BASE_URI__ ;
        $baseUrl = $host. Tools::substr($requestUri, 0, $pos) . "choixRelais&";
    }

    // traductions
    $translations = array(
        'choice' => array(
            'fr' => 'Choix',
            'en' => 'Choice',
            'es' => 'Elección'
        ),
        'monday' => array(
            'fr' => 'lundi',
            'en' => 'monday',
            'es' => 'lunes'
        ),
        'tuesday' => array(
            'fr' => 'mardi',
            'en' => 'tuesday',
            'es' => 'martes'
        ),
        'wednesday' => array(
            'fr' => 'mercredi',
            'en' => 'wednesday',
            'es' => 'miércoles'
        ),
        'thursday' => array(
            'fr' => 'jeudi',
            'en' => 'thursday',
            'es' => 'jueves'
        ),
        'friday' => array(
            'fr' => 'vendredi',
            'en' => 'friday',
            'es' => 'viernes'
        ),
        'saturday' => array(
            'fr' => 'samedi',
            'en' => 'saturday',
            'es' => 'sábado'
        ),
        'sunday' => array(
            'fr' => 'dimanche',
            'en' => 'sunday',
            'es' => 'domingo'
        ),
        'postal_code' => array(
            'fr' => 'Code Postal',
            'en' => 'Postal Code',
            'es' => 'Código Postal'
        ),
        'city' => array(
            'fr' => 'Ville',
            'en' => 'City',
            'es' => 'Ciudad'
        ),
        'search' => array(
            'fr' => 'RECHERCHER',
            'en' => 'SEARCH',
            'es' => 'BUSCAR'
        ),
        'opening' => array(
            'fr' => "Horaires d'ouverture",
            'en' => 'Opening hours',
            'es' => 'Horario de apertura'
        ),
        'selecting' => array(
            'fr' => "JE S&Eacute;LECTIONNE",
            'en' => 'SELECT THIS POINT',
            'es' => 'SELECCIONAR ESTE PUNTO'
        ),
        'choose' => array(
            'fr' => "Choisir ce ".$relayName,
            'en' => 'Choose this '.$relayName,
            'es' => 'Elegir este '.$relayName
        ),
        'code' => array(
            'fr' => "Code du ".$relayName." :",
            'en' => $relayName.' code:',
            'es' => 'Código de '.$relayName.':'
        ),
    );
    ?>
    <!DOCTYPE HTML>
    <html lang="<?php echo $locale; ?>">
    <head>
    <meta charset="UTF-8">
    <title><?php echo $translations['choice'][$lang].' '.$relayName; ?></title>
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.js"></script>
    <script type="text/javascript" src="//maps.google.com/maps/api/js"></script>
    <script type="text/javascript">
        translations = {
            daysLetter:[
                "<?php echo $translations['monday'][$lang]; ?>",
                "<?php echo $translations['tuesday'][$lang]; ?>",
                "<?php echo $translations['wednesday'][$lang]; ?>",
                "<?php echo $translations['thursday'][$lang]; ?>",
                "<?php echo $translations['friday'][$lang]; ?>",
                "<?php echo $translations['saturday'][$lang]; ?>",
                "<?php echo $translations['sunday'][$lang]; ?>"],
            openingHours: "<?php echo $translations['opening'][$lang]; ?>",
            selecting: "<?php echo $translations['selecting'][$lang]; ?>",
            choose: "<?php echo $translations['choose'][$lang]; ?>",
            code: "<?php echo $translations['code'][$lang]; ?>",
        };
    </script>
    <script type="text/javascript" src="<?php echo $host; ?>modules/envoimoinscher/views/js/choix-relais.js"></script>
    </head>
    <body>
    <div class="container" style="width:100%;">
    <div class="row">
        <div class="col-xs-12">
            <div class="bg-top-box pt20 pb20 pl40 pr40">
                <div id="map" class="mt20">
                    <div class="row mb20">
                        <input type="hidden" id="host" value="<?php echo $host; ?>" />
                        <input type="hidden" id="urlBase" value="<?php echo $baseUrl; ?>" />
                        <input type="hidden" id="ptrel-ope" value="<?php echo $ope; ?>" />
                        <input type="hidden" id="ptrel-srv" value="<?php echo $srv; ?>" />
                        <input type="hidden" id="ptrel-poids" value="0.72" />
                        <input type="hidden" id="ptrel-pays" value="<?php echo $country; ?>" />
                        <input type="hidden" id="ptrel-inputCallBack" value="<?php echo $inputCallBack; ?>" />
                        <div class="col-xs-3">
                        <input type="text" id="ptrel-cp" class="form-control input-sm"
                            placeholder="<?php echo $translations['postal_code'][$lang]; ?>"
                            value="<?php echo $cp; ?>"/>
                        </div>
                        <div class="col-xs-4">
                            <input type="text" id="ptrel-ville" class="form-control input-sm"
                            placeholder="<?php echo $translations['city'][$lang]; ?>"
                            value="<?php echo $ville; ?>"/>
                        </div>
                        <a class="pointer" >
                            <div id="submitNewMap" class='col-xs-3 btn btn-primary btn-sm' style="width:160px;">
                                <?php echo $translations['search'][$lang]; ?>
                            </div>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-xs-9">
                            <div id="map-canvas" style="height:500px;"></div>
                        </div>
                        <div class="col-xs-3 pl10"  style="height:500px;overflow-y:scroll">
                            <table class="table table-hover">
                                <tbody id="rightcol-ptrel">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- eod col-xs-9  -->
    </div><!-- eod row bg-grey  -->
    </div><!-- eod container  -->
    </body>
    <style type="text/css">
        .pt20 {
            padding-top: 20px;
        }
        .pb20 {
            padding-bottom: 20px;
        }
        .pl40 {
            padding-left: 40px;
        }
        .pr40 {
            padding-right: 40px;
        }
        .mb20 {
            margin-bottom: 20px;
        }
        .mt20 {
            margin-top: 20px;
        }
        .pointer {
            cursor: pointer;
        }
        .bg-top-box {
            background: #FFF;
            padding-top: 10px;
            /*
            border-bottom: 1px solid #C9C9C9;
            border-right: 1px solid #C9C9C9;
            border-left: 1px solid #C9C9C9;
            */
        }
        .popover-hour + .popover {
            max-width: 300px;
        }
        .db{
            display:block;
        }
    </style>
    </html>
    <?php
}
