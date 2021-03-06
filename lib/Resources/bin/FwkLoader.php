<?php

use Doctrine\Common\ClassLoader, Doctrine\ORM\Configuration, Doctrine\ORM\EntityManager;

/**
 * Classe de lancement / récuperation des fichiers necessaires au FrameWork
 *
 * @author f.mithieux
 */
class FwkLoader {

  private static $twigCachePath = 'lib/Resources/Twig/Cache/';

  /**
   * Se connecte à la BDD et récupère l'entity manager
   *
   * @return EntityManager
   */
  public static function getEntityManager() {

    require_once __DIR__ . '/../Doctrine/Doctrine/Common/ClassLoader.php';
    require_once __DIR__ . '/../Doctrine/Extensions/TablePrefix.php';

    $doctrineClassLoader = new ClassLoader('Doctrine', __DIR__ . '/../Doctrine');
    $doctrineClassLoader->register();
    $fwkEntitiesClassLoader = new ClassLoader('Resources\Entities', __DIR__ . '/../../../lib');
    $fwkEntitiesClassLoader->register();
    $entitiesClassLoader = new ClassLoader('Entities', __DIR__ . '/../../../lib');
    $entitiesClassLoader->register();

    $config = new Configuration;
    $config->addCustomNumericFunction('RAND', 'Doctrine\ORM\Query\AST\Functions\Rand');
    //$cache = new ApcCache;
    //$config->setMetadataCacheImpl($cache);
    $driverImpl = $config->newDefaultAnnotationDriver(array(__DIR__ . '/../../../lib/Entities', __DIR__ . '/../../../lib/Resources/Entities'));
    $config->setMetadataDriverImpl($driverImpl);
    $config->setProxyDir(__DIR__ . '/../Doctrine/Doctrine/Proxies');
    $config->setProxyNamespace('Proxies');
    //$config->setQueryCacheImpl($cache);

    $evm = new \Doctrine\Common\EventManager();

    $tablePrefix = new \DoctrineExtensions\TablePrefix(PDO_PREFIX);
    $evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);

    $dbParams = array('host' => PDO_HOST, 'driver' => PDO_DRIVER, 'port' => PDO_PORT, 'dbname' => PDO_DATABASE_NAME, 'user' => PDO_USER, 'password' => PDO_PASSWORD,);

    return EntityManager::create($dbParams, $config, $evm);
  }

  /**
   * Lis l'ensemble des classes du FrameWork et les retourne
   *
   * @return array
   */
  public static function getFwkEntities() {

    $arrayClasses = array();

    $entitiesDirFwk = PATH_TO_IMPORTANT_FILES . 'lib/Resources/Entities/';
    $dir2           = opendir($entitiesDirFwk);

    while ($file = readdir($dir2)) {
      if ($file != '.' && $file != '..' && !is_dir($entitiesDirFwk . $file) && !preg_match('#Repository#', $file)) {
        $file           = preg_replace('#\.php#', '', $file);
        $arrayClasses[] = $file;
      }
    }

    closedir($dir2);

    return $arrayClasses;
  }

  /**
   * Lis l'ensemble des classes du FrameWork et les retourne
   *
   * @return array
   */
  public static function getFwkEntitiesLower() {

    $arrayClasses = array();

    $entitiesDirFwk = PATH_TO_IMPORTANT_FILES . 'lib/Resources/Entities/';
    $dir2           = opendir($entitiesDirFwk);

    while ($file = readdir($dir2)) {
      if ($file != '.' && $file != '..' && !is_dir($entitiesDirFwk . $file) && !preg_match('#Repository#', $file)) {
        $file           = preg_replace('#\.php#', '', $file);
        $arrayClasses[] = strtolower($file);
      }
    }

    closedir($dir2);

    return $arrayClasses;
  }

  /**
   * Récupère l'environnement Twig
   *
   * @param $path
   * @return Twig_Environment
   */
  public static function getTwigEnvironement($path = '') {

    include_once(PATH_TO_IMPORTANT_FILES . 'lib/Resources/Twig/Autoloader.php');
    Twig_Autoloader::register();

    if (BACKOFFICE_ACTIVE === 'gestion/')
      $viewDirectory = PATH_TO_BACKOFFICE_FILES . 'web/'; else
      $viewDirectory = PATH_TO_IMPORTANT_FILES . 'web/';

    // Dossier contenant les templates
    $loader = new Twig_Loader_Filesystem($viewDirectory);
    unset($viewDirectory);

    if (BACKOFFICE_ACTIVE != 'gestion/')
      return new Twig_Environment($loader, array('cache' => (TWIG_CACHE_ACTIVE ? PATH_TO_IMPORTANT_FILES . self::$twigCachePath : false))); else
      return new Twig_Environment($loader, array('cache' => false));
  }

  /**
   * Fonction de lancement du FrameWork
   *
   * Appelle toutes les classes essentielles
   */
  public static function getContext($path = null) {

    // Heure du serveur
    date_default_timezone_set(TIMEZONE);

    self::coreLoader();
    self::utilsLoader();
    // Gestion des erreurs en fonction de l'environnement
    FwkErrorHandler::execute(PATH_TO_IMPORTANT_FILES . 'logs/errors/' . date("Y-m-d") . '.log');

    if (!is_null($path)) {

      // Appel des Filters pour la gestion de sessions ou autre
      require_once $path . 'app/filters.php';

      /*
       * Appel du routing.php qui gèrera l'appel aux classes de controllers
       * Si on utilise les controllers PHP on fait appel au routing_dev
       */
      require_once $path . 'app/routing.php';
    }
    unset($path);
  }

  private static function utilsLoader() {

    // Gestion des dates
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Utils/DateUtils.php';

    // Gestion de calculs mathématiques / monaie etc..
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Utils/MathUtils.php';

    // Appel à la boite à outils du fwk
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Utils/FwkUtils.php';

    // Classe custom utile uniquement dans le projet (notament pour l'envoi de mails)
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Utils/FwkCustom.php';

    // Classe custom nécessaire pour la génération de pagination custom
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Utils/PaginationBuilder.php';
  }

  private static function coreLoader() {

    // Gestion globale du framework
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/FwkManager.php';

    // Gestion globale des filtres
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/FilterManager.php';

    // Gestion globale des controllers
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/ControllerManager.php';

    // Gestion globale des controllers ajax
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/AjaxManager.php';

    // Classe générant des tableaux (BO)
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/FwkTable.php';

    // Classe permettant le dessin de GChart
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/FwkGChart.php';

    // Classe permettant la gestion de logs
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/FwkLog.php';

    // Classe permettant la gestion d'erreurs PHP
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/FwkErrorHandler.php';

    // Classe permettant la securisation des mots de passe
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/FwkSecurity.php';

    // Classe SwiftMailer, permettant l'envoi d'emails
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/SwiftMailer/lib/swift_required.php';

    // Classe d''upload de fichiers
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/FwkUpload.php';

    // Gestion des filtres Twig supplémentaires
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/TwigCustomFilters.php';

    // Classe de resizing d'images
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/WideImage/WideImage.php';

    // Classe gérant les URLs du blog
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Core/BlogManager.php';

    // Classe de parsing de dom
    require_once PATH_TO_IMPORTANT_FILES . 'lib/Resources/Simplehtmldom/simple_html_dom.php';
  }

}
