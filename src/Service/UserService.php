<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use DateTime;

/**
 * This class is used to separate the database operations from websocket 
 * operations. When websocket gets the desired status from the client-side
 * this class will be called and perform all necessary database operations
 * and return the result as an array data.
 * 
 * @global object $em
 *   Entity manager interface instance of the Doctrine class.
 * 
 * @method constructor()
 *   This constructor is used to initialize the objects.
 * @method getUserByEmail()
 *   This function is used to fetch user data from database.
 */
class UserService
{
  /**
   * Entity Manager class object that manages the persistence and 
   * retrieval of entity objects from the database.
   * 
   * @var object
   */
  private $em;
  /**
   * This constructor is used to initialize the the entity manager interface.
   *
   * @param EntityManagerInterface $entityManagerInterface
   *   This entity manager interface is used to manipulate the database.
   */
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->em = $entityManager;
  }

  /**
   * Get user email is called to fetch user information if the user is active
   * by the email of the user.
   *
   * @param string $email
   *   Email is used as a unique identifier for the database.
   * 
   * @return mixed
   *   This function returns the array of user information and if the user is
   *   not found it returns FALSE.
   */
  public function getUserByEmail(string $email)
  {
    $userRow = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

    if ($userRow) {

      // SET the user is activated and CURRENT DATETIME.
      $userRow->setIsActive(true);
      $userRow->setLastActiveTime(new DateTime);
      $this->em->persist($userRow);
      $this->em->flush();

      // Get the list of users from the database or session
      // For example, using Doctrine ORM:
      $users = $this->em->getRepository(User::class)->findBy(['isActive' => TRUE]);

      $userList = [];
      // Iterating the users list to individual users list.
      foreach ($users as $user) {
        $userList[] = array(
          'fullName'       => $user->getFullName(),
          'img'            => $user->getImageName(),
          'lastActiveTime' => $user->getLastActiveTime(),
          'userId'         => $user->getId()
        );
      }
      return $userList;
    }
    return FALSE;
  }
}