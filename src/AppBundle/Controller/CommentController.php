<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use AppBundle\Entity\Comment;

class CommentController extends Controller
{
    /**
     * @Route("comment/create")
     */
    public function createAction()
    {
        $comment = new Comment();
        $comment->setName('Test1');
        $comment->setDescription('Desc');
        $comment->setTitle('Title');
        $comment->setLikeQnt(3);
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        /**
         * @var $aclProvider \Symfony\Component\Security\Acl\Dbal\MutableAclProvider
         * @var $acl Acl
         */
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($comment);
        $acl = $aclProvider->createAcl($objectIdentity);

        $tokenStorage = $this->get('security.token_storage');
        $user = $tokenStorage->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        //$acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $acl->insertObjectFieldAce('title', $securityIdentity, MaskBuilder::MASK_VIEW, 0);
        $aclProvider->updateAcl($acl);


        return $this->render('AppBundle:Comment:create.html.twig', array(
            // ...
        ));
    }

}
