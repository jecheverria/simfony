<?php

namespace JUV\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JUV\UserBundle\Entity\User;
use JUV\UserBundle\Form\UserType;

class UserController extends Controller
{
    public function indexAction()
    {
        $juv = $this->getDoctrine()->getManager();
        
        $users = $juv->getRepository('JUVUserBundle:User')->findAll();
        
        return $this->render('JUVUserBundle:User:index.html.twig', array('users' => $users));
    
    }

    public function addAction()
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        
        return $this->render('JUVUserBundle:User:add.html.twig', array('form' => $form->createView()));
    }
    
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateURL('juv_user_create'),
            'method' => 'POST'));
            
        return $form;
    }
    
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            $password = $form->get('password')->getData();
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            
            $user->setPassword($encoded);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('juv_user_index');
        }
        return $this->render('JUVUserBundle:User:add.html.twig', array('form' => $form->createView()));
    }

    public function viewAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('JUVUserBundle:User');
        
        $user = $repository->findOneById($id);
        
        return new Response('<b>Usuario:</b> ' . $user->getUsername());
    }
    
}
