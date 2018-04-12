<?php

namespace CG\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use CG\PortfolioBundle\Entity\Message;
use CG\PortfolioBundle\Entity\User;
use CG\PortfolioBundle\Form\MessageType;
use CG\PortfolioBundle\Entity\Article;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render("CGPortfolioBundle:Default:index.html.twig");
    }
    
    public function lastArticlesAction()
    {
        $articles = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Article")->getLastArticles();
        return $this->render("CGPortfolioBundle:Default:last_articles.html.twig", array('articles' => $articles));        
    }
    
    public function surveyAction()
    {
        return $this->render("CGPortfolioBundle:Default:survey.html.twig");
    }
    
    public function aboutAction()
    {
        // Récupération des tous les skills
        $em = $this->getDoctrine()->getManager();
        $skills = $em->getRepository("CGPortfolioBundle:Skill")->findAll();
        $experiences = $em->getRepository("CGPortfolioBundle:Experience")->findAll();
        return $this->render("CGPortfolioBundle:Default:about.html.twig", array('skills' => $skills, 'experiences' => $experiences));
    }
    
    public function educationAction()
    {
       $em = $this->getDoctrine()->getManager();
       $educations = $em->getRepository("CGPortfolioBundle:Education")->getAllEducations();
       return $this->render("CGPortfolioBundle:Default:education.html.twig", array('educations' => $educations));
    }
    
    public function contactAction(Request $request)
    {
        $message = new Message();
        $message->setSeen(false);
        
        // Création du formulaire à partir de l'entité Message
        $form = $this->createForm(MessageType::class, $message);

        
        // Si méthode est POST et que le formulaire de contact est bien renseigné
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Merci, votre message a bien été envoyé, je vous répondrai dès que possible.');
            return $this->redirectToRoute("cg_portfolio_home");
        } else {
            return $this->render("CGPortfolioBundle:Default:contact.html.twig", array('form' => $form->createView()));      
        }
    }
    
    public function cvAction()
    {
        return $this->render("CGPortfolioBundle:Default:cv.html.twig");
    }
    
    public function articleAction()
    {
        $articles = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Article")->getAllArticles();
        return $this->render("CGPortfolioBundle:Default:article.html.twig", array("articles" => $articles));
    }
    
    public function experienceShowAction(Request $request, $id)
    {
        $experience = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Experience")->find($id);
        if(null === $experience) {
            $request->getSession()->getFlashBag()->add('notice', 'Erreur, Expérience non trouvée.');
            return $this->redirectToRoute("cg_portfolio_about");
        } else {
            $seo = $this->container->get('sonata.seo.page');
            $seo->setTitle($experience->getCompany());
            $seo-> addMeta('name', 'description', $experience->getCompany());
            $seo-> addMeta('name', 'keywords', $experience->getCompany());     
            return $this->render("CGPortfolioBundle:Default:experience_show.html.twig", array('experience' => $experience));
        }
    }
    
    public function skillShowAction(Request $request, $id)
    {
        $skill = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Skill")->find($id);
        if(null === $skill) {
            $request->getSession()->getFlashBag()->add('notice', 'Erreur, Skill non trouvé.');
            return $this->redirectToRoute("cg_portfolio_about");
        } else {
            $seo = $this->container->get('sonata.seo.page');
            $seo->setTitle($skill->getName());
            $seo-> addMeta('name', 'description', $skill->getName());
            $seo-> addMeta('name', 'keywords', $skill->getName());            
            return $this->render("CGPortfolioBundle:Default:skill_show.html.twig", array('skill' => $skill));
        }
    }
    public function educationShowAction(Request $request, $id)
    {
        $education = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Education")->find($id);
        if(null === $education) {
            $request->getSession()->getFlashBag()->add('notice', 'Erreur, Formation non trouvée.');
            return $this->redirectToRoute("cg_portfolio_about");
        } else {
            $seo = $this->container->get('sonata.seo.page');
            $seo->setTitle($education->getTitle());
            $seo-> addMeta('name', 'description', $education->getTitle());
            $seo-> addMeta('name', 'keywords', $education->getTitle());
            return $this->render("CGPortfolioBundle:Default:education_show.html.twig", array('education' => $education));
        }
    }
    public function articleShowAction(Request $request, $slug)
    {
        $article = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Article")->findOneBy(array("slug" => $slug));
        if(null === $article) {
            $request->getSession()->getFlashBag()->add('notice', 'Erreur, Article non trouvé.');
            return $this->redirectToRoute("cg_portfolio_article");
        } else {
            $keywords = array();
            foreach($article->getKeywords()  as  $k) {
                $keywords[] = $k->getName();
            }            
            $seo = $this->container->get('sonata.seo.page');
            $seo->setTitle($article->getTitre());
            $seo-> addMeta('name', 'description', $article->getTitre());
            $seo-> addMeta('name', 'keywords', implode(",", $keywords));
            return $this->render("CGPortfolioBundle:Default:article_show.html.twig", array('article' => $article));
        }
    } 
}
