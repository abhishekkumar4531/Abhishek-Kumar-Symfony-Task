<?php

namespace App\Service;

class FetchData {

  /**
   * userData - It will store the user's personal data.
   *
   * @var array
   */
  private $userData = Array();

  /**
   * fetchUserProfile
   * This function is communicates with database and also display user's credentials.
   *
   * @param  mixed $verify
   * @param  mixed $request
   * @param  mixed $userEmail
   *
   * It will be verify that the is $userEmail is exiting or not
   * if exists then fetch all the user's credentials and
   * then render back to edit profile page with all the credentails.
   * @return void
   */
  public function fetchUserProfile($userRepo, $request, $userEmail) {
    $session = $request->getSession();
    $fetchCredentials = $userRepo->findOneBy([ 'userEmail' => $userEmail ]);

    //If $fetchCredentials will not return null that means user exits,
    //then fetch all the data after that render to the edit profile page with values.
    //If due to any reason $fetchCredentails return null then redirect to the home page.
    if($fetchCredentials) {
      $fetchImage = $fetchCredentials->getUserImage();
      $session->set('user_image', $fetchImage);
      /*$fetchBio = $fetchCredentials->getUserBio();
      $fetchFirstName = $fetchCredentials->getUserFirstName();
      $fetchLastName = $fetchCredentials->getUserLastName();
      $fetchMobile = $fetchCredentials->getUserMobile();
      $fetchEmail = $fetchCredentials->getUserEmail();*/

      $userData['userImage'] = $fetchImage;
      $userData['userBio'] = $fetchCredentials->getUserBio();
      $userData['userFirstName'] = $fetchCredentials->getUserFirstName();
      $userData['userLastName'] = $fetchCredentials->getUserLastName();
      $userData['userMobile'] = $fetchCredentials->getUserMobile();
      $userData['userEmail'] =$fetchCredentials->getUserEmail();

      return $userData;
    }
    return null;
  }

  /**
   * arrangePostData - This is for display the user's post with condtion of maximum
   * 10 posts can be load at a time
   *
   * @param mixed $fetchUsers
   * @param array $posts
   * @param int $count
   * @return array
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
