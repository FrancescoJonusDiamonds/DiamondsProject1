<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use App\Form\UserFormCreate;
/**
 * User controller.
 * @Route("/api", name="api_")
 */
class UserController extends FOSRestController
{
	
	
  /**
   * Lists all current users from User.
   * @Rest\Get("/users")
   *
   * @return Response
   */
  public function getUserAction()
  {
    $repository = $this->getDoctrine()->getRepository(User::class);
    $users = $repository->findAll();
	
	// Here I show a different method to execute a query on database, and I use it to find the total number of users
	
	$em = $this->getDoctrine()->getManager();
	
    $usersTemp = $em->getRepository(User::class);
        
    // Query how many rows are there in the User table
	
    $usersNumber = $usersTemp->createQueryBuilder('a')
    // Filter by some parameter if you want
    // ->where('a.name = 'john'')
		->select('count(a.id)')
        ->getQuery()
        ->getSingleScalarResult();
	
    return $this->handleView($this->view($usersNumber,$users));
  }
  
  /**
   * Return only one user from User.
   * @Rest\Get("/user/:id")
   *
   * @return Response
   */
   
   
  public function getSingleUserAction(Request $request)
  {
    $repository = $this->getDoctrine()->getRepository(User::class);
	$id = json_decode($request->getContent(), true);
    $user = $repository->find($id);
	
	if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }
	
    return $this->handleView($this->view($user));
	
  }
  
  
  /**
   * Create User.
   * @Rest\Post("/user")
   *
   * @return Response
   */
  public function postUserAction(Request $request)
  {
    $user = new User();
    $form = $this->createForm(UserFormCreate::class, $user);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();
      return $this->handleView($this->view($user));
    }
    return $this->handleView($this->view($form->getErrors()));
  }
  
  /**
   * Check if a user can do login (and is in database with the given password) and return a auth key true or false
   * @Rest\Post("/user/authenticate")
   *
   * @return Response
   */
  
  public function getSingleUserAuth(Request $request)
  {
    $repository = $this->getDoctrine()->getRepository(User::class);
	$data = json_decode($request->getContent(), true);
	$username = $data[0];
	$password = $data[1];
	
	$em = $this->getDoctrine()->getManager();
	
    $usersTemp = $em->getRepository(User::class);
        
    $userFound = $usersTemp->createQueryBuilder('a')

		->where("a.username = $username && a.password = $password")
		->select('count(a.id)')
        ->getQuery()
        ->getSingleScalarResult();
		
	authKey = false;
	
	if ($userFound == 1) authKey = true;
	
	
    return $this->handleView($this->view($authKey));
	
	}
	
	
	/**
   * Update User.
   * @Rest\Put("/user")
   *
   * @return Response
   */
   
   
  public function putUpdateUser(Request $request)
  {
	  
	$repository = $this->getDoctrine()->getRepository(User::class);
	$data = json_decode($request->getContent(), true);
	$id = $data[0];
	$name = $data[1];
	$username = $data[2];
	$password = $data[3];
    $user = $repository->find($id);
	
	if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }
		
	$user->setName($name);
	$user->setUsername($username);
	$user->setPassword($password);
	$user->setUpdated(new Assert\DateTime());
	
	return $this->handleView($this->view($user));
    
  }
  
}