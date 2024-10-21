<?php 

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="My API", version="0.1")
 */

/**
 * @OA\Get(
 *     path="http://localhost:8000/api/blog_articles",
 *     @OA\Response(response="200", description="Get all blog articles")
 * )
 */


 /**
 * @OA\Get(
 *     path="http://localhost:8000/api/blog_articles/{id}",
 *     @OA\Response(response="200", description="Get a blog article by his id")
 * )
 */

 /**
 * @OA\Post(
 *     path="http://localhost:8000/api/blog_articles",
 *     @OA\Response(response="200", description="Create a new blog article")
 * )
 */

 /**
 * @OA\Patch(
 *     path="http://localhost:8000/api/blog_articles/{id}",
 *     @OA\Response(response="200", description="Update a blog article")
 * )
 */

  /**
 * @OA\Delete(
 *     path="http://localhost:8000/api/blog_articles/{id}",
 *     @OA\Response(response="200", description="Delete a blog article")
 * )
 */