<?php 
    namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class HomeController extends Controller{

        
        /**
         * @Route("/", name="homepage")
         */
        public function home(){
            $prenom=["alain","rayan","issas"];
            return $this->render("home.html.twig",[
                'title'=>"You'r welcom",
                'age'=>31,
                'tableau'=>$prenom
            ]);
        }
    }
?>