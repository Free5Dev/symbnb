<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Form\ImageType;
use App\Entity\Image;

use App\Repository\AdRepository;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        // $repo=$this->getDoctrine()->getRepository(Ad::class);
        $ads=$repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads'=>$ads
        ]);
    }
    /**
     * Permet de créer une annonce
     *@Route("/ads/new", name="ads_create")
     *Permet de verifier que l'utilisateur est bien connecté avant d'ajouter l'annonce 
     *@isGranted("ROLE_USER")
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager){
        $ad= new Ad();
       
        $form=$this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }
            //relier l'annonce à l'utlisateur connecté avant de persister et de flush
            $ad->setAuthor($this->getUser());
            $manager->persist($ad);
            $manager->flush();
            // ajouter un message flash pour informer l'utilisateur que tous vas bien
            $this->addFlash(
                'success',
                "L'annonce <strong> {$ad->getTitle()} </strong> a bien été enresistrée !"
            );
            
            return $this->redirectToRoute("ads_show",[
                'slug'=>$ad->getSlug()
            ]);
        }
        return $this->render('ad/new.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * Permet d'editer une annonce
     *@Route("/ads/{slug}/edit", name="ads_edit")
     *@Security("is_granted('ROLE_USER') and user=== ad.getAuthor()", message="Cette annonce ne vous appartient pas , vous ne pouvez pas la modifier")
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager){

        $form=$this->createForm(AdType::class, $ad);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();
            // ajouter un message flash pour informer l'utilisateur que tous vas bien
            $this->addFlash(
                'success',
                "Les modification de L'annonce <strong> {$ad->getTitle()} </strong> ont bien été enresistrée !"
            );
            
            return $this->redirectToRoute("ads_show",[
                'slug'=>$ad->getSlug()
            ]);
        }
        return $this->render('ad/edit.html.twig',[
            'form'=>$form->createView(),
            'ad'=>$ad
        ]);
    }
    /**
     * Permet d'afficher le slug d'une annonce
     *@Route("/ads/{slug}", name="ads_show")
     * @return Response
     */
    public function show(Ad $ad){
        // je recupere l'annonce qui correspond au slug
        // $ad=$repo->findOneBySlug($slug);
        return $this->render("ad/show.html.twig",[
            'ad'=>$ad
        ]);
    }
    /**
     * Permet de supprimer une annonce
     *@Route("/ads/{slug}/delete", name="ads_delete")
     *@Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous n'avez pas le droit d'acceder à cette ressource")
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delele(Ad $ad, ObjectManager $manager){
        // suppression de l'annonce qui l'appartient
        $manager->remove($ad);
        $manager->flush();
        // message flash
        $this->addFlash(
            'success',
            "L'annonce <strong>{ad.getTitle()} </strong> à bien été supprimer"
        );
        return $this->redirectToRoute('ads_index');
    }
    
}
