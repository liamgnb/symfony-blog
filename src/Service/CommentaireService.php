<?php

namespace App\Service;

use App\Entity\Commentaire;
use App\Repository\AuteurRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

class CommentaireService
{
    private CommentaireRepository $commentaireRepository;
    private AuteurRepository $auteurRepository;

    /**
     * @param CommentaireRepository $commentaireRepository
     * @param AuteurRepository $auteurRepository
     */
    public function __construct(CommentaireRepository $commentaireRepository, AuteurRepository $auteurRepository)
    {
        $this->commentaireRepository = $commentaireRepository;
        $this->auteurRepository = $auteurRepository;
    }


    public function addCommentaire(Commentaire $commentaire, $formCommentaire) : int
    {
        // Vérification du contenu
        if(!$commentaire->getContenu()){
            $formCommentaire->get('contenu')->addError(new FormError('Veuillez saisir un contenu'));
            return 1;
        }

        // vérrification du nom si pseudo saisie
        if(!$formCommentaire->get('auteur')->get('pseudo')->isEmpty() && $formCommentaire->get('auteur')->get('nom')->isEmpty()){
            $formCommentaire->get('auteur')->get('nom')->addError(new FormError('Veuillez saisir le nom'));
            return 1;
        }
        // vérification du prénim si pseudo saisie
        if(!$formCommentaire->get('auteur')->get('pseudo')->isEmpty() && $formCommentaire->get('auteur')->get('prenom')->isEmpty()){
            $formCommentaire->get('auteur')->get('prenom')->addError(new FormError('Veuillez saisir le prénom'));
            return 1;
        }

        // Ajout de l'auteur si il n'est pas présent dans la table.
        if($commentaire->getAuteur()->getPseudo() && !$this->auteurRepository->findOneBy(['pseudo' => $commentaire->getAuteur()->getPseudo()])){
            $this->auteurRepository->add($commentaire->getAuteur(), true);
        } elseif (!$commentaire->getAuteur()->getPseudo()){
            $commentaire->setAuteur(null);
        } else {
            $commentaire->setAuteur($this->auteurRepository->findOneBy(['pseudo' => $commentaire->getAuteur()->getPseudo()]));
        }
        $commentaire->setCreatedAt(new \DateTime());
        $this->commentaireRepository->add($commentaire, true);
        return 2;
    }

}