<?php
/**
 * Created by Hugues
 * 08/01/2019
 */

namespace App\Service;

use App\Entity\User;

/**
 * Checks permissions over the whole admin area
 * First checks if a user has ROLE_STAFF. (If not --> redirect to /admin/login page)
 * If he / she is logged in AND has ROLE_STAFF --> get first name, last name, and the center / club where he works.
 * Used for displaying user information in the admin area, and the tasks he / she has to undertake.
 */
class AdminUserService
{

    /**
     * @param User $user
     * @return array
     */
    public function getLegitimateUser($logged_user) {

		if ($logged_user instanceof User) {
			
		   	$role = $logged_user->getRoles();
			// Verification Roles :
			$isAdminOrStaff = (in_array('ROLE_ADMIN', $role) || 
								in_array('ROLE_STAFF', $role)) ? true : false;
			
			// getting information from a legitimate user with ROLE_STAFF
	
			$firstName = $logged_user->getFirstName();
			$lastName = $logged_user->getLastName();
			
			
			$isAdmin = (in_array("ROLE_ADMIN", $role)) ? "oui" : "non";
			
			if (is_null($logged_user->getCenter())) {
				$center = 'Admin';
			} else {
				$center = $logged_user->getCenter()->getName();
			}
	
			$userData = [
				'firstName' => $firstName,
				'lastName' => $lastName,
				'center' => $center,
				'isAdmin' => $isAdmin
			];
	
			$datas = ($isAdminOrStaff) ? $userData : false ;
			
			return $datas;
		}else{
			return false;
		}


    }

}