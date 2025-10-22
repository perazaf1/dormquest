```mermaid
%% Diagramme de classes - DormQuest

classDiagram
    class Utilisateur {
        +int id
        +string nom
        +string prenom
        +string email
        +string motDePasse
        +string role
        +string photoProfil
        +creerCompte()
        +seConnecter()
        +modifierProfil()
    }

    class Etudiant {
        +string villeRecherche
        +float budget
        +ajouterFavori(Annonce)
        +envoyerCandidature()
    }

    class Loueur {
        +string typeLoueur  // Particulier, Agence, CROUS
        +creerAnnonce()
        +modifierAnnonce()
        +supprimerAnnonce()
    }

    class Annonce {
        +int idAnnonce
        +string titre
        +string description
        +string adresse
        +float prix
        +float superficie
        +bool colocationPossible
        +string dateDisponibilite
        +ajouterPhoto()
    }

    class Favori {
        +int idFavori
        +date dateAjout
    }

    class Filtre {
        +string localisation
        +float budgetMax
        +string typeLogement
        +bool accesPMR
        +bool eligibleAPL
    }

    class Message {
        +int idMessage
        +string contenu
        +date dateEnvoi
    }

    Utilisateur <|-- Etudiant
    Utilisateur <|-- Loueur
    Loueur "1" --> "*" Annonce : "publie"
    Etudiant "1" --> "*" Favori : "ajoute"
    Etudiant "1" --> "*" Message : "envoie"
    Loueur "1" --> "*" Message : "reçoit"
    Annonce "1" --> "*" Favori : "est ajouté à"
    Etudiant --> Filtre : "utilise"
