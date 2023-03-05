<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PerformedOperations;
use App\Service\Cryptography;
use App\Service\SendEmail;
use App\Service\Cookie;
use App\Entity\User;

class HomeController extends AbstractController
{
  /**
   * Entity Manager class object that manages the persistence and 
   * retrieval of entity objects from the database.
   * 
   * @var object
   */
  private $em;
  /**
   * This object provides different functions for user operations.
   * 
   * @var object
   */
  private $performOperation;

  /**
   * This is the object of User class which is a entity having setter and 
   * getter methods.
   * 
   * @var object
   */
  private $user;

  /**
   * Cryptography object encode and decode values before
   * sending in link or storing password.
   *
   * @var object
   */
  private $cryptography;
  /**
   * This object variable is used to call sendMail function.
   *
   * @var object
   */
  private $sendMail;
  /**
   * OTP is the Entity which stores username, otp and otp created at time.
   *
   * @var object
   */
  private $otp;
  /**
   * This image by default contains the profile avatar.
   *
   * @var object
   */
  private $imageName = '';
  /**
   * This object is used to store and retrieve cookie. 
   *
   * @var object
   */
  private $cookie;
  /**
   * Constructor is initializing the objects.
   *
   * @param object $em
   *   EntityManagerInterface is used to manage entity with database
   *   it helps to alter database easily.
   *    
   * @return void
   *   Contractor does not return anything instead it is used to initialize
   *   the object.
   */
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
    $this->cryptography = new Cryptography();
    $this->performOperation = new PerformedOperations();
    $this->cookie = new Cookie();
    $this->user = new User();
    $this->sendMail = new SendEmail();
  }

  /**
   * Home controller is the main feed that will be shown to the user, at one
   * side of the screen online user's will be present and on the other side 
   * posts will be present.
   *   
   * @Route("/home", name="home")
   *   This route is for sending user to the home screen.
   * 
   * @param object $request
   *   Request object handles parameter from query parameter.
   * 
   * @return Response
   *   Response the view which contains user stored information.
   */
  public function home(Request $request): Response
  {
    if (!$this->cookie->isActive($request)) {
      return $this->redirectToRoute('loginUser');
    }
    return $this->render('home/index.html.twig');
  }

  /**
   * This root redirects user to home page, home pages if the user is already
   * logged in or not if not then user will be redirected to login page.
   *   
   * @Route("/", name="root")
   *   This route is for sending user to the home screen.
   * 
   * @return Response
   *   This response will be to the home screen.
   */
  public function rootPage(): Response
  {
    return $this->redirectToRoute('home');
  }
  /**
   * This root redirects user to home page, home pages if the user is already
   * logged in or not if not then user will be redirected to login page.
   *   
   * @Route("/active-users", name="active-users")
   *   This route is for sending user to the home screen.
   * 
   * @return Response
   *   This response will be to the home screen.
   */
  public function sendEmail(Request $request): Response
  {
    $email = $this->cookie->getCookie('email', $request);
    $userRow = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

    return new JsonResponse(
      [
        'email' => $email,
        'userImage' => $userRow->getImageName()
      ]
    );
  }
}