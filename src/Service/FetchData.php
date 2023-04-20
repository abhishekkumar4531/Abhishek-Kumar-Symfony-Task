<?php

namespace App\Service;

/**
 * FetchData
 */
class FetchData {

  /**
   * userData - It will store the user's personal data.
   *
   * @var array
   */
  private $userData = Array();

  /**
   * This function is communicates with database and also display user's
   * credentials.
   * It will be verify that the is $userEmail is exiting or not
   * if exists then fetch all the user's credentials and
   * then render back to edit profile page with all the credentails.
   *
   *   @param  mixed $userRepo
   *     It store the object of UserRepository class and also fetch data from
   *     database.
   *   @param  mixed $request
   *     This Request object is to handles the session.
   *   @param  string $userEmail
   *     It store the user email.
   *   @return array
   *     If user exits then it will reurn an array $userData which consists user's
   *     data otherwise null.
   */
  public function fetchUserProfile($userRepo, $request, $userEmail) {
    $session = $request->getSession();
    $fetchCredentials = $userRepo->findOneBy([ 'userEmail' => $userEmail ]);

    // If $fetchCredentials will not return null that means user exits,
    // then fetch all the data after that render to the edit profile page with
    // values.
    // If due to any reason $fetchCredentails return null then redirect to the
    // home page.
    if($fetchCredentials) {
      $fetchImage = $fetchCredentials->getUserImage();
      $session->set('user_image', $fetchImage);
      /*$fetchBio = $fetchCredentials->getUserBio();
      $fetchFirstName = $fetchCredentials->getUserFirstName();
      $fetchLastName = $fetchCredentials->getUserLastName();
      $fetchMobile = $fetchCredentials->getUserMobile();
      $fetchEmail = $fetchCredentials->getUserEmail();*/

      $this->userData['userImage'] = $fetchImage;
      $this->userData['userBio'] = $fetchCredentials->getUserBio();
      $this->userData['userFirstName'] = $fetchCredentials->getUserFirstName();
      $this->userData['userLastName'] = $fetchCredentials->getUserLastName();
      $this->userData['userMobile'] = $fetchCredentials->getUserMobile();
      $this->userData['userEmail'] =$fetchCredentials->getUserEmail();

      return $this->userData;
    }
    return null;
  }

  /**
   * This is for display the user's post with condtion of maximum
   * 10 posts can be load at a time
   *
   *   @param  mixed $userRepo
   *     It store the object of UserRepository class and also fetch data from
   *     database.
   *   @param  array $posts
   *     It stores the all the post data of database.
   *   @param  int $count
   *     It stores the number of post already desplayed on home page.
   *
   *   @return array
   *     It will return an array $mediaData[] which consists post data.
   */
  public function arrangePostData($userRepo, $posts, $count) {
    $count += 10;
    $start = 0;
    $mediaData = [];
    foreach($posts as $user) {
      $start++;
      if($start > $count) {
        return $mediaData;
      }
      $users = [];
      $userEmail = $user->getUserEmail();
      $userPostComment = $user->getPostComment();
      $users['postComment'] = $userPostComment;
      $userPostFile = $user->getPostFile();
      $users['postFile'] = $userPostFile;
      $userInfo = $userRepo->findOneBy([ 'userEmail' => $userEmail ]);
      $userId = $userInfo->getId();
      $users['userId'] = $userId;
      $userImage = $userInfo->getUserImage();
      $users['userImage'] = $userImage;
      $userFirstName = $userInfo->getUserFirstName();
      $users['userFirstName'] = $userFirstName;
      $userLastName = $userInfo->getUserLastName();
      $users['userLastName'] = $userLastName;
      $mediaData[] = $users;
    }
    return $mediaData;
  }

}

?>
