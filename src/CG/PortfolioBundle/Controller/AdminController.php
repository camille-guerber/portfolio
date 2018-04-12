<?php

namespace CG\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use CG\PortfolioBundle\Entity\Message;
use CG\PortfolioBundle\Entity\User;
use CG\PortfolioBundle\Entity\Skill;
use CG\PortfolioBundle\Entity\Experience;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use CG\PortfolioBundle\Form\SkillType;
use CG\PortfolioBundle\Form\ExperienceType;
use CG\PortfolioBundle\Entity\Education;
use CG\PortfolioBundle\Form\EducationType;
use CG\PortfolioBundle\Form\UserType;
use CG\PortfolioBundle\Entity\Article;
use CG\PortfolioBundle\Form\ArticleType;
use CG\PortfolioBundle\Entity\Keyword;
use CG\PortfolioBundle\Form\KeywordType;

class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
        if($this->checkAdmin($request)) {
            return $this->render('CGPortfolioBundle:Admin:admin.html.twig');
            
        } else {
            $this->addFlash('notice', "Erreur, vous n'êtes pas connecté");
            return $this->redirectToRoute("cg_portfolio_admin_login");
        }
    }
    
    public function loginAction(Request $request)
    {
        $session = new Session();
        if(empty($session->get('user'))) {
            $user = new User();
            $form = $this->get('form.factory')->create(FormType::class, $user)
                    ->add('name', TextType::class)
                    ->add('password', PasswordType::class)
                    ->add('Se connecter', SubmitType::class);

            if($request->isMethod("POST") && $form->HandleRequest($request)->isValid()) {

                $userFound = $this->getDoctrine()->getManager()->getRepository('CGPortfolioBundle:User')->findOneBy(
                        array(
                            'name' => $user->getName()
                        ));

                if(null === $userFound) {
                   $this->addFlash('notice', 'Erreur, utilisateur introuvable');
                } else {
                    if($user->getPassword() == $userFound->getPassword()) {
                        if($userFound->getAdmin() == 1) {
                            $session->set('user', $userFound);
                            $this->addFlash('notice', 'Vous êtes bien connecté en tant que : '.$userFound->getName());
                            return $this->redirectToRoute('cg_portfolio_admin');                           
                        } else {
                            $this->addFlash('notice', "Erreur : ".$userFound->getName()." n'est pas adminstrateur");
                            return $this->redirectToRoute('cg_portfolio_admin_login ');                            
                        }
                    } else {
                        $this->addFlash('notice', 'Erreur, mot de passe ou identifiant invalide');
                    }
                }
            }
            return $this->render("CGPortfolioBundle:Admin:login.html.twig", array(
                'form' => $form->createView()
            ));           
        } else {
            $this->addFlash('notice', 'Erreur, vous êtes déjà connecté');
            return $this->redirectToRoute('cg_portfolio_home');
        }
    }
    
    public function logoutAction(Request $request)
    {
        $session = new Session();
        
        if(!empty($session->get('user'))) {
            $session->remove('user');
            $this->addFlash('notice', 'Succès, vous avez bien été déconnecté');
            return $this->redirectToRoute('cg_portfolio_home');
        } else {
            $this->addFlash('notice', "Erreur, vous n'êtes pas connecté");
            return $this->redirectToRoute("cg_portfolio_home");
        }
    }

    public function checkAdmin(Request $request)
    {
        $session = new Session();
        if(($session->get('user'))) {
           return $session->get('user')->getAdmin(); 
        } else {
            return 0;
        }
    }

    public function usersAction(Request $request)
    {   
        if(!$this->checkAdmin($request)) {
            $this->addFlash('notice', "Erreur, vous n'êtes pas autorisé à consulter cette page");
            return $this->redirectToRoute('cg_portfolio_home');
        }
        $users = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:User")->findAll();
        return $this->render("CGPortfolioBundle:Admin:users.html.twig", array('users' => $users));
    }
    
    public function userAddAction(Request $request)
    {
        $user = new User();
        
        // Création du formulaire à partir de l'entité User
        $form = $this->createForm(UserType::class, $user)
                ->add("submit", SubmitType::class, array("label" => "Ajouter"));
        
        // Si méthode POST, formulaire est bien renseigné et valide
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('warning', 'Utilisateur correctement ajouté.');
            return $this->redirectToRoute('cg_portfolio_admin_users');
        } else {
            $this->addFlash('warning', 'Veuillez remplir le formulaire correctement.');
            return $this->render("CGPortfolioBundle:Admin:user_add.html.twig", array(
              'form' => $form->createView()
           ));       
        }
    }
    
    public function userEditAction(Request $request, $id)
    {
        $user = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:User")->find($id);
        
        if($user === null) {
            $this->getSession()->getFlashBag()->add('notice', "Erreur l'utilisateur portant l'id ".$id." n'a pas été trouvé.");
            return $this->redirectToRoute("cg_portfolio_admin_users");
        } else {
            $form = $this->createForm(UserType::class, $user)
                    ->add('submit', SubmitType::class, array("label" => "Editer"));
        }
        
        // Si méthode est POST et que le formulaire est bien renseigné
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('notice', 'Utilisateur bien édité !');
            return $this->redirectToRoute("cg_portfolio_admin_users");
        } else {
            return $this->render("CGPortfolioBundle:Admin:user_edit.html.twig", array('form_user_edit' => $form->createView()));
        }
    }
    
    public function userDeleteAction(Request $request, $id)
    {   
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("CGPortfolioBundle:User")->find($id);
        if(null === $user) {
            $this->getSession()->getFlashBag()->add('notice', "Erreur l'utilisateur portant l'id : ".$id." n'existe pas.");
            return $this->redirectToRoute("cg_portfolio_admin_users");
        } else {
            $em->remove($user);
            $em->flush();
            $this->addFlash("notice","Succès l'utilisateur a bien été supprimé.");
            return $this->redirectToRoute("cg_portfolio_admin_users");
        }
    }
    
    public function messagesAction()
    {
        $messages = $this->getDoctrine()->getManager()->getRepository('CGPortfolioBundle:Message')->getAllMessages('DESC');
        return $this->render('CGPortfolioBundle:Admin:messages.html.twig', array('messages' => $messages));
    }
    
    public function deleteMessageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('CGPortfolioBundle:Message')->find($id);

        if(null === $message) {
           $this->addFlash('notice', "Erreur le message portant l'ID". $id ." n'existe pas!");
           return $this->redirectToRoute("cg_portfolio_admin_messages");
        } else {
            $em->remove($message);
            $em->flush();
            $this->addFlash('notice', "Le message portant l'ID ". $id ." a bien supprimé!");
            return $this->redirectToRoute("cg_portfolio_admin_messages");
        }
    }
    
    public function sendMessageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("CGPortfolioBundle:Message")->find($id);
        $this->addFlash('notice', "Le Message a bien été envoyé à ".$user->getEmail());
        return $this->redirectToRoute("cg_portfolio_admin_messages");
    }
    
    public function seenMessageAction(Request $request, $id)
    {
       $em = $this->getDoctrine()->getManager();
       $message = $em->getRepository("CGPortfolioBundle:Message")->find($id);
       if(null === $message) {
        $this->addFlash('notice', "Erreur");
        return $this->redirectToRoute("cg_portfolio_admin_messages");
       } else {
           $message->setSeen(true);
           $em->flush();
           $this->addFlash('notice', "Message marqué comme lu !");
           return $this->redirectToRoute("cg_portfolio_admin_messages");
       }
    }
    
    public function skillsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $skills = $em->getRepository("CGPortfolioBundle:Skill")->getAllSkills();
        if(null === $skills) {
             $this->addFlash('notice', "Aucun skill ppur le moment !");
             return $this->redirectToRoute("cg_portfolio_admin");
        } else {
            return $this->render("CGPortfolioBundle:Admin:skills.html.twig", array('skills' => $skills));
        }
    }
    
    public function skillAddAction(Request $request)
    {
        $skill = new Skill();
        
        // Création du formulaire à partir de l'entité Message
        $form = $this->createForm(SkillType::class, $skill)
                ->add("submit", SubmitType::class, array("label" => "Ajouter"));
        
        // Si méthode POST, formulaire est bien renseigné et valide
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();
            $this->addFlash('warning', 'Skill correctement ajouté.');
            return $this->redirectToRoute('cg_portfolio_admin_skills');
        } else {
            $this->addFlash('warning', 'Veuillez remplir le formulaire correctement.');
            return $this->render("CGPortfolioBundle:Admin:skill_add.html.twig", array(
              'form' => $form->createView()
           ));       
        }
    }
    
    public function skillEditAction(Request $request, $id)
    {
        $skill= $this->getDoctrine()->getManager()->getRepository('CGPortfolioBundle:Skill')->find($id);
        if(null === $skill) {
            $this->addFlash('notice', 'Skill non trouvé !');
            return $this->redirectToRoute('cg_portfolio_admin_skills');
        } else {

            $form = $this->createForm(SkillType::class, $skill);

            // Si méthode est POST et que le formulaire est bien renseigné
            if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('notice', 'Skill bien édité !');
                return $this->redirectToRoute("cg_portfolio_admin_skills");
            } else {
                return $this->render("CGPortfolioBundle:Admin:skill_edit.html.twig", array('form_skill_edit' => $form->createView()));
            }
        }
    }
    
    public function skillDeleteAction(Request $request, $id)
    {
        $skill = $this->getDoctrine()->getManager()->getRepository('CGPortfolioBundle:Skill')->find($id);
        if(null === $skill) {
            $this->addFlash('notice', 'Skill non trouvé !');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($skill);
            $em->flush();
            $this->addFlash('notice', 'Skill bien supprimé');         
        }
        return $this->redirectToRoute('cg_portfolio_admin_skills');
    }
    
    public function experiencesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $experiences = $em->getRepository("CGPortfolioBundle:Experience")->getAllExperiences();
        if(null === $experiences) {
            $this->getSession()->getFlashBag()->add('notice', 'Aucune expérience pour le moment, veuillez en ajouter une.');
            return $this->redirectToRoute("cg_portfolio_admin_experience_add");
        }
        return $this->render("CGPortfolioBundle:Admin:experiences.html.twig", array('experiences' => $experiences));
    }
    
    public function experienceAddAction(Request $request)
    {
       $em = $this->getDoctrine()->getManager();
       $experience = new Experience();
       $skills = $em->getRepository("CGPortfolioBundle:Skill")->findAll();
       
       foreach($skills as $skill) {
           $experience->addSkill($skill);
       }
       
       $form = $this->createForm(ExperienceType::class, $experience)
               ->add("submit", SubmitType::class, array("label" => "Ajouter"));
        // Si méthode POST, formulaire est bien renseigné et valide
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
            $em->persist($experience);
            $em->flush();
            $this->addFlash('notice', 'Expérience correctement ajoutée.');
            return $this->redirectToRoute('cg_portfolio_admin_experiences');
        } else {
            $this->addFlash('warning', 'Veuillez remplir le formulaire correctement.');
            return $this->render("CGPortfolioBundle:Admin:experience_add.html.twig", array(
              'form' => $form->createView()
           ));       
        }
    }
    
    public function experienceEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $experience = $em->getRepository("CGPortfolioBundle:Experience")->find($id);
        if(null === $experience) {
            $this->getSession->getFlashBag()->add('warning', 'Expérience non trouvée.');
            return $this->redirectToRoute("cg_portfolio_admin_experiences");
        } else {
            $form = $this->createForm(ExperienceType::class, $experience);
            
            // Si méthode est POST et que le formulaire est bien renseigné
            if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('notice', 'Expérience bien éditée !');
                return $this->redirectToRoute("cg_portfolio_admin_experiences");
            } else {
                return $this->render("CGPortfolioBundle:Admin:experience_edit.html.twig", array('form_experience_edit' => $form->createView()));
            }            
        }
    }
    
    public function experienceDeleteAction(Request $request, $id)
    {
        $experience = $this->getDoctrine()->getManager()->getRepository('CGPortfolioBundle:Experience')->find($id);
        if(null === $experience) {
            $this->addFlash('warning', 'Expérience non trouvée !');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($experience);
            $em->flush();
            $this->addFlash('notice', 'Expérience bien supprimée');         
        }
        return $this->redirectToRoute('cg_portfolio_admin_experiences');
    }
    
    public function educationsAction(Request $request)
    {
        $educations = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Education")->findAll();
        
        if(null === $educations) {
            $this->getSession()->getFlashBag()->add("warning", "Aucune formation ajoutée pour le moment, veuillez en ajouter une.");
            return $this->redirectToRoute("cg_portfolio_admin_education_add");
        }
        
        return $this->render("CGPortfolioBundle:Admin:educations.html.twig", array("educations" => $educations));
    }
    
    public function educationAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $education = new Education;
        $form = $this->createForm(EducationType::class, $education);
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
            $em->persist($education);
            $em->flush();
            $this->addFlash('notice', 'Formation correctement ajoutée.');
            return $this->redirectToRoute('cg_portfolio_admin_educations');
        } else {
            $this->addFlash('warning', 'Veuillez remplir le formulaire correctement.');
            return $this->render("CGPortfolioBundle:Admin:education_add.html.twig", array(
              'form' => $form->createView()
           ));       
        }
    }
    
    public function educationEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $education = $em->getRepository("CGPortfolioBundle:Education")->find($id);
        if(null === $education) {
            $this->getSession->getFlashBag()->add('warning', 'Formation non trouvée.');
            return $this->redirectToRoute("cg_portfolio_admin_education");
        } else {
            $form = $this->createForm(EducationType::class, $education)
                    ->add("submit", SubmitType::class, array("label" => "Modifier"));
            
            // Si méthode est POST et que le formulaire est bien renseigné
            if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('notice', 'Formation bien éditée !');
                return $this->redirectToRoute("cg_portfolio_admin_educations");
            } else {
                return $this->render("CGPortfolioBundle:Admin:education_edit.html.twig", array('form_education_edit' => $form->createView()));
            }            
        }        
    }
    
    public function educationDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $education = $em->getRepository("CGPortfolioBundle:Education")->find($id);
        if(null === $education) {
            $this->addFlash("warning", "Erreur, la formation n'existe pas.");
        } else {
            $em->remove($education);
            $em->flush();
            $this->addFlash("notice", "La formation a bien été supprimée.");            
        }
        return $this->redirectToRoute("cg_portfolio_admin_educations");
    }

    # Article #
    public function articlesAction(Request $request)
    {
        $articles = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Article")->findAll();
        
        if(null === $articles) {
            $this->getSession()->getFlashBag()->add("warning", "Aucun article  trouvé pour le moment, veuillez en créer un.");
            return $this->redirectToRoute("cg_portfolio_admin_article_add");
        }
        
        return $this->render("CGPortfolioBundle:Admin:articles.html.twig", array("articles" => $articles));
    }
    
    public function articleAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
            $article->setSlug($article->slugify($article->getTitre()));
            $article->setDateCreation(new \DateTime());
            $em->persist($article);
            $em->flush();
            $this->addFlash('notice', 'Article correctement ajoutée.');
            return $this->redirectToRoute('cg_portfolio_admin_articles');
        } else {
            $this->addFlash('warning', 'Veuillez remplir le formulaire correctement.');
            return $this->render("CGPortfolioBundle:Admin:article_add.html.twig", array(
              'form' => $form->createView()
           ));       
        }        
    }
    
    public function articleEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $article = $em->getRepository("CGPortfolioBundle:Article")->find($id);
        if(null === $article) {
            $this->getSession->getFlashBag()->add('warning', 'Article non trouvé.');
            return $this->redirectToRoute("cg_portfolio_admin_articles");
        } else {
            $form = $this->createForm(ArticleType::class, $article)
                    ->add("submit", SubmitType::class, array("label" => "Modifier"));
            
            // Si méthode est POST et que le formulaire est bien renseigné
            if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
                $article->setSlug($article->slugify($article->getTitre()));
                $article->setDateMaj(new \DateTime());
                $em->flush();
                $this->addFlash('notice', 'Article bien éditée !');
                return $this->redirectToRoute("cg_portfolio_admin_articles");
            } else {
                return $this->render("CGPortfolioBundle:Admin:article_edit.html.twig", array('form_article_edit' => $form->createView()));
            }            
        }        
    }
    
    public function articleDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository("CGPortfolioBundle:Article")->find($id);
        if(null === $article) {
            $this->addFlash("warning", "Erreur, l'article n'existe pas.");
        } else {
            $em->remove($article);
            $em->flush();
            $this->addFlash("notice", "L'article a bien été supprimé.");            
        }
        return $this->redirectToRoute("cg_portfolio_admin_articles");
    }

    # Keyword #
    public function keywordsAction(Request $request)
    {
        $keywords = $this->getDoctrine()->getManager()->getRepository("CGPortfolioBundle:Keyword")->findAll();
        
        if(null === $keywords) {
            $this->getSession()->getFlashBag()->add("warning", "Aucun mot clé  trouvé pour le moment, veuillez en créer.");
            return $this->redirectToRoute("cg_portfolio_admin_keyword_add");
        }
        
        return $this->render("CGPortfolioBundle:Admin:keywords.html.twig", array("keywords" => $keywords));
    }
    
    public function keywordAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $keyword = new \CG\PortfolioBundle\Entity\Keyword();
        $form = $this->createForm(KeywordType::class, $keyword);
        if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
            $keyword->setSize(strlen($keyword->getName()));
            $em->persist($keyword);
            $em->flush();
            $this->addFlash('notice', 'Mot clé correctement ajouté.');
            return $this->redirectToRoute('cg_portfolio_admin_keywords');
        } else {
            $this->addFlash('warning', 'Veuillez remplir le formulaire correctement.');
            return $this->render("CGPortfolioBundle:Admin:keyword_add.html.twig", array(
              'form' => $form->createView()
           ));       
        }        
    }
    
    public function keywordEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $keyword = $em->getRepository("CGPortfolioBundle:Keyword")->find($id);
        if(null === $keyword) {
            $this->getSession->getFlashBag()->add('warning', 'Mot clé non trouvé.');
            return $this->redirectToRoute("cg_portfolio_admin_keywords");
        } else {
            $form = $this->createForm(KeywordType::class, $keyword)
                    ->add("submit", SubmitType::class, array("label" => "Modifier"));
            
            // Si méthode est POST et que le formulaire est bien renseigné
            if($request->isMethod("POST") && $form->handleRequest($request)->isValid()) {
                $keyword->setSize(strlen($keyword->getName()));
                $em->flush();
                $this->addFlash('notice', 'Mot clé bien éditée !');
                return $this->redirectToRoute("cg_portfolio_admin_keywords");
            } else {
                return $this->render("CGPortfolioBundle:Admin:keyword_edit.html.twig", array('form_keyword_edit' => $form->createView()));
            }
        }        
    }
    
    public function keywordDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $keyword = $em->getRepository("CGPortfolioBundle:Keyword")->find($id);
        if(null === $keyword) {
            $this->addFlash("warning", "Erreur, le mot clé n'existe pas.");
        } else {
            $em->remove($keyword);
            $em->flush();
            $this->addFlash("notice", "Le mot clé a bien été supprimé.");            
        }
        return $this->redirectToRoute("cg_portfolio_admin_keywords");
    }
}
