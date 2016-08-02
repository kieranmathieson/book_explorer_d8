<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 8/1/16
 * Time: 11:26 AM
 */

namespace Drupal\book_explorer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\book\BookManagerInterface;

/**
 * Class ResearchController
 * @package Drupal\book_menus\Controller
 *
 * This class is for experimenting with booky things.
 */

class ResearchController extends ControllerBase {

  /**
   * @var BookManagerInterface
   */
  private $bookManager;

  /**
   * @var |Drupal\Core\Entity\entityTypeManagerInterface
   */
  protected $entTypeManager;
  function __construct(
      BookManagerInterface $bookManager,
      EntityTypeManagerInterface $entTypeManager) {
    $this->bookManager = $bookManager;
    $this->entTypeManager = $entTypeManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('book.manager'),
      $container->get('entity_type.manager')
    );
  }


  public function listBooks() {
//    $book_controller = \Drupal::service('book.manager');
//    $books = $book_controller->getAllBooks();

    $books = $this->bookManager->getAllBooks();

    ksm($books);

    $book_list = '<p>Book titles:</p><ul>';

    foreach ($books as $book) {
      $book_list .=
        '<li>'
          . \Drupal\Core\Link::fromTextAndUrl(
              $book['title'] . $this->t(' (Structure)'),
              \Drupal\Core\Url::fromRoute('book_menus.book_structure', ['bid' => $book['bid']])
            )->toString() . ' '
          . \Drupal\Core\Link::fromTextAndUrl(
            $book['title'] . $this->t(' (Rendered)'),
            \Drupal\Core\Url::fromRoute('book_menus.book_rendered', ['bid' => $book['bid']])
          )->toString()
        . '</li>';
    }

    $book_list .= '</ul>';

    return [
      '#type' => 'markup',
      '#markup' =>

        $book_list,
    ];
  }

  public function showBookStructure($bid) {
    $book_node = $this->entTypeManager->getStorage('node')->load($bid);
    $structure = $this->bookManager->bookTreeAllData($bid);
    ksm('Book node:');
    ksm($book_node);
    ksm('Book structure:');
    ksm($structure);

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Structure of @bookTitle',
        ['@bookTitle' => $book_node->getTitle()]),
    ];

  }

  public function showBookRendered($bid) {
    $book_node = $this->entTypeManager->getStorage('node')->load($bid);
    $structure = $this->bookManager->bookTreeAllData($bid);
    $output = $this->bookManager->bookTreeOutput($structure);
    ksm($output);
    return [
      $output
    ];

  }

  public function inspectMenu() {
//    $menu_link_manager = new \Drupal\Core\Menu\MenuLinkManager()

//    $db_conn = \Drupal::service('database');
//    $cache_menu = \Drupal::service('cache.menu');
//    $invalidator = \Drupal::service('cache_tags.invalidator');
//    $table = 'menu_tree';
//    $treeStorage = new \Drupal\Core\Menu\MenuTreeStorage($db_conn, $cache_menu, $invalidator, $table);

    $linkTree = \Drupal::service('menu.link_tree');
//    ksm('link tree');
//    ksm($linkTree);

    $menu_link_manager = \Drupal::service('plugin.manager.menu.link'); //$linkTree->menuLinkManager;
    $menu_name = 'evil_menu';
    $in_use = $menu_link_manager->menuNameInUse($menu_name) ? 'Yes' : 'No';

//    $tools = $linkTree->load('tools', new MenuTreeParameters());
//
//    ksm('Tools');
//    ksm($tools);

    return [
      '#type' => 'markup',
      '#markup' => 'DOGZZZZ! ' . $menu_name . ' in use: ' . $in_use,
    ];

  }

}