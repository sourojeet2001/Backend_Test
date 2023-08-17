<?php

declare(strict_types = 1);

namespace Drupal\myapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for TestMod routes.
 */
final class FirstController extends ControllerBase {

  protected $entityTypeManager;

  protected $conn;

  /**
   *
   */
  public function __construct(EntityTypeManager $entityTypeManager, Connection $conn) {
    $this->entityTypeManager = $entityTypeManager;
    $this->conn = $conn;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('database'),
    );
  }

  /**
   *
   */
  public function build() {

    $blogs = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'blogs']);
    $data = [];
    foreach ($blogs as $blog) {
      $title = $blog->get('title')->getValue()[0]['value'];
      $body = $blog->get('body')->getValue()[0]['value'];
      $date = $blog->get('created')->getValue()[0]['value'];
      $tags_field = $blog->get('field_blog_tags');
      $tags = [];
      foreach ($tags_field->getValue() as $item) {
        $tags[] = $item['target_id'];
      }
      $timestamp = (int) $date;
      $formatted_date = date("m/d/Y h:i:s A T", $timestamp);
      $author = $blog->getOwnerId();
      // $db = \Drupal::database();
      $query = $this->conn->select('users_field_data', 'ufd')
        ->fields('ufd', ['name'])
        ->condition('uid', $author, '=');
      $result = $query->execute()->fetchField();

      $terms = Term::loadMultiple($tags);
      $termNames = [];
      foreach ($terms as $term) {
        $termNames[] = $term->get('name')->getValue()[0]['value'];
      }

      $data[] = [
        'title' => $title,
        'body' => $body,
        'date' => $formatted_date,
        'author' => $result,
        'tags' => $termNames,
      ];
    }
    $response = new JsonResponse($data, 200);
    return $response;
  }

}
