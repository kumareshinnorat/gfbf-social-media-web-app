<?php

namespace App\Service;

use DateTimeImmutable;
use Exception;
use DateTime;

/**
 * This class is for different methods that will be performed inside 
 * controller. 
 * 
 * @method storeImg()
 *   Store user image in the database.
 *  
 * @author Kumaresh Baksi <kumaresh.baksi@innoraft.com>
 */
class PerformedOperations
{
  /**
   * This function stores user image in the project directory.
   *
   * @param string $name
   *  This variable is the unique user name of the user.
   * @param string $location
   *  This variable is the location of the project directory where image
   *  will be stored.
   * @param object $image
   *  This variable is the object of the user image.
   * 
   * @return mixed
   *  If function stored the image successfully it returns name of the image
   *  if it does not then it will return FALSE otherwise.
   */
  public function storeImg(string $name, string $location, object $image)
  {
    $name = $name . "." . $image->guessExtension();
    try {
      $image->move($location, $name);
    } catch (Exception $ex) {
      return FALSE;
    }
    return $name;
  }

  /**
   * This function generates random number for OTP.
   *
   * @return int
   *  rand function returns integer.
   */
  public function generateOtp()
  {
    return rand(1000, 9999);
  }
  /**
   * This function return date and time in a immutable format.
   *
   * @return DateTimeImmutable
   *  Returning date and time in a immutable format.
   */
  public function currentTime()
  {
    return DateTimeImmutable::createFromFormat(DateTime::RFC3339, (new DateTime())->format(DateTime::RFC3339));
  }
}